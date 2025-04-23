<?php

namespace App\Controller;

use App\Service\CMFBanksApiService;
use App\Service\ExcelExportService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DollarExchangeController extends AbstractController
{
    private const int ITEMS_PER_PAGE = 10;

    private CMFBanksApiService $service;
    private ExcelExportService $excelService;

    public function __construct(CMFBanksApiService $service, ExcelExportService $excelService)
    {
        $this->service = $service;
        $this->excelService = $excelService;
    }

    #[Route('/', name: 'app_dollar_rates')]
    public function index(Request $request): Response | Exception
    {
        $rates = [];
        $year = $request->query->get('year', date('Y'));
        $month = $request->query->get('month', date('m'));
        $format = $request->query->get('format', 'html');
        $page = max(1, (int) $request->query->get('page', 1));
        $message = null;

        try {
            $rates = $this->service->dollarExchange($year, $month);

            if ($format === 'excel') {
                return $this->generateExcelResponse($rates, $year, $month);
            }

            return $this->renderDollarExchangeView($rates, $year, $month, $message, $page);
        } catch (Exception $e) {
            $message = $e->getMessage();

            if ($format === 'excel') {
                throw $this->createNotFoundException($message);
            }

            return $this->renderDollarExchangeView($rates, $year, $month, $message, $page);
        }
    }

    private function generateExcelResponse(array $rates, string $year, string $month): Response
    {
        $fileName = "dollar_rates_{$year}_$month.xlsx";
        $content = $this->excelService->createExcel($rates);
        
        $response = new Response($content);
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $fileName . '"');
        $response->headers->set('Cache-Control', 'max-age=0');
        
        return $response;
    }

    private function renderDollarExchangeView(array $rates, string $year, string $month, ?string $error = null, int $page = 1): Response
    {
        return $this->render('dollar_exchange/index.html.twig', [
            'rates' => $rates,
            'year' => $year,
            'month' => $month,
            'error' => $error,
            'page' => $page,
            'limit' => self::ITEMS_PER_PAGE,
        ]);
    }
}
