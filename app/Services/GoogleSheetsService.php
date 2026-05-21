<?php

namespace App\Services;

use App\Models\Order;
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;
use Google\Service\Sheets\BatchUpdateSpreadsheetRequest;
use Google\Service\Sheets\Request as SheetsRequest;
use Google\Service\Sheets\DimensionRange;
use Google\Service\Sheets\CellFormat;
use Google\Service\Sheets\CellData;
use Google\Service\Sheets\RowData;
use Google\Service\Sheets\TextFormat;
use Google\Service\Sheets\GridRange;
use Google\Service\Sheets\RepeatCellRequest;
use Google\Service\Sheets\Color;

class GoogleSheetsService
{
    protected Sheets $service;
    protected string $spreadsheetId;
    protected string $sheet     = 'Sheet1';
    protected int    $sheetId   = 0; // default Sheet1 ID

    public function __construct()
    {
        $credentialsPath = storage_path('app/google-credentials.json');

        // Kalau ada di env variable, tulis ke file sementara
        if (!file_exists($credentialsPath) && config('services.google_sheets.credentials_json')) {
            file_put_contents($credentialsPath, config('services.google_sheets.credentials_json'));
        }

        if (!file_exists($credentialsPath)) {
            throw new \Exception('File google-credentials.json tidak ditemukan di storage/app/');
        }

        $client = new Client();
        $client->setAuthConfig($credentialsPath);
        $client->addScope(Sheets::SPREADSHEETS);

        $this->service       = new Sheets($client);
        $this->spreadsheetId = config('services.google_sheets.spreadsheet_id');

        if (empty($this->spreadsheetId)) {
            throw new \Exception('GOOGLE_SHEETS_ID belum diset di .env');
        }

        $this->ensureHeader();
    }

    /**
     * Buat header + format jika sheet masih kosong
     */
    protected function ensureHeader(): void
    {
        $response = $this->service->spreadsheets_values->get(
            $this->spreadsheetId,
            "{$this->sheet}!A1:F1"
        );

        if (!empty($response->getValues())) {
            return;
        }

        // Tulis header
        $header = [['ID Pesanan', 'Tanggal', 'Nama Pelanggan', 'Item Pesanan', 'Total (Rp)', 'Metode Bayar']];
        $this->service->spreadsheets_values->update(
            $this->spreadsheetId,
            "{$this->sheet}!A1:F1",
            new ValueRange(['values' => $header]),
            ['valueInputOption' => 'RAW']
        );

        // Format header: bold, background hijau tua, teks putih
        $headerFormat = new CellFormat([
            'textFormat'       => new TextFormat(['bold' => true, 'foregroundColor' => new Color(['red' => 1, 'green' => 1, 'blue' => 1])]),
            'backgroundColor'  => new Color(['red' => 0.1, 'green' => 0.23, 'blue' => 0.1]),
            'horizontalAlignment' => 'CENTER',
        ]);

        $requests = [
            // Bold & warna header
            new SheetsRequest([
                'repeatCell' => new RepeatCellRequest([
                    'range'  => new GridRange([
                        'sheetId'          => $this->sheetId,
                        'startRowIndex'    => 0,
                        'endRowIndex'      => 1,
                        'startColumnIndex' => 0,
                        'endColumnIndex'   => 6,
                    ]),
                    'cell'   => new CellData(['userEnteredFormat' => $headerFormat]),
                    'fields' => 'userEnteredFormat(textFormat,backgroundColor,horizontalAlignment)',
                ]),
            ]),
            // Freeze baris pertama
            new SheetsRequest([
                'updateSheetProperties' => [
                    'properties' => [
                        'sheetId'    => $this->sheetId,
                        'gridProperties' => ['frozenRowCount' => 1],
                    ],
                    'fields' => 'gridProperties.frozenRowCount',
                ],
            ]),
        ];

        $this->service->spreadsheets->batchUpdate(
            $this->spreadsheetId,
            new BatchUpdateSpreadsheetRequest(['requests' => $requests])
        );

        // Auto-resize semua kolom
        $this->autoResize();
    }

    /**
     * Append satu baris pesanan yang sudah completed
     */
    public function appendOrder(Order $order): void
    {
        $itemsSummary = $order->items->map(function ($item) {
            return ($item->menu->name ?? 'Menu dihapus') . ' x' . $item->quantity;
        })->join(', ');

        $row = [
            $order->id,
            $order->created_at->format('d/m/Y H:i'),
            $order->customer_name,
            $itemsSummary,
            'Rp ' . number_format((float) $order->total_price, 0, ',', '.'),
            strtoupper($order->payment_method ?? '-'),
        ];

        $this->service->spreadsheets_values->append(
            $this->spreadsheetId,
            "{$this->sheet}!A:F",
            new ValueRange(['values' => [$row]]),
            ['valueInputOption' => 'USER_ENTERED']
        );

        // Center semua data (baris 2 ke bawah)
        $centerFormat = new CellFormat(['horizontalAlignment' => 'CENTER']);
        $this->service->spreadsheets->batchUpdate(
            $this->spreadsheetId,
            new BatchUpdateSpreadsheetRequest([
                'requests' => [
                    new SheetsRequest([
                        'repeatCell' => new RepeatCellRequest([
                            'range'  => new GridRange([
                                'sheetId'          => $this->sheetId,
                                'startRowIndex'    => 1, // skip header
                                'startColumnIndex' => 0,
                                'endColumnIndex'   => 6,
                            ]),
                            'cell'   => new CellData(['userEnteredFormat' => $centerFormat]),
                            'fields' => 'userEnteredFormat.horizontalAlignment',
                        ]),
                    ]),
                ],
            ])
        );

        $this->autoResize();
    }

    /**
     * Auto-resize semua kolom agar konten tidak terpotong
     */
    protected function autoResize(): void
    {
        // Set lebar kolom secara manual (dalam pixel)
        $widths = [
            0 => 90,   // ID Pesanan
            1 => 140,  // Tanggal
            2 => 160,  // Nama Pelanggan
            3 => 320,  // Item Pesanan
            4 => 120,  // Total (Rp)
            5 => 120,  // Metode Bayar
        ];

        $requests = [];
        foreach ($widths as $colIndex => $pixelSize) {
            $requests[] = new SheetsRequest([
                'updateDimensionProperties' => [
                    'range' => new DimensionRange([
                        'sheetId'    => $this->sheetId,
                        'dimension'  => 'COLUMNS',
                        'startIndex' => $colIndex,
                        'endIndex'   => $colIndex + 1,
                    ]),
                    'properties' => ['pixelSize' => $pixelSize],
                    'fields'     => 'pixelSize',
                ],
            ]);
        }

        $this->service->spreadsheets->batchUpdate(
            $this->spreadsheetId,
            new BatchUpdateSpreadsheetRequest(['requests' => $requests])
        );
    }
}
