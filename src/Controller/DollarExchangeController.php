<?php

namespace App\Controller;

use App\Service\CMFBanksApiService;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class DollarExchangeController extends AbstractController
{
    private CMFBanksApiService $service;

    public function __construct(CMFBanksApiService $service)
    {
        $this->service = $service;
    }

    #[Route('/', name: 'app_dollar_rates')]
    public function index(Request $request): Response|array|false
    {
        $rates = [];
        $year = $request->query->get('year', date('Y'));
        $month = $request->query->get('month', date('m'));
        $format = $request->query->get('format', 'html');

        try {
            $rates = $this->service->dollarExchange($year, $month);

            if ($format === 'excel') {
                return $this->exportToExcel($rates, $year, $month);
            }

            return $this->render('dollar_exchange/index.html.twig', [
                'rates' => $rates,
                'year' => $year,
                'month' => $month,
            ]);
        } catch (\Exception $e) { /* ignored lines */ }

        return $this->render('dollar_exchange/index.html.twig', [
            'rates' => $rates,
            'year' => $year,
            'month' => $month,
        ]);
    }

    private function exportToExcel(array $rates, string $year, string $month): array|false
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Fecha');
        $sheet->setCellValue('B1', 'Valor');

        $row = 2;

        foreach ($rates as $rate) {
            $sheet->setCellValue('A' . $row, $rate['fecha']);
            $sheet->setCellValue('B' . $row, $rate['valor']);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = "dollar_rates_{$year}_$month.xlsx";
        $tempFile = tempnam(sys_get_temp_dir(), 'dollar_exchange');
        $writer->save($tempFile);

        $response = file($tempFile);

        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $fileName
        );

        return $response;
    }
}
