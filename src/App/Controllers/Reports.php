<?php

namespace Src\App\Controllers;

use Src\App\Models\ReportsModel;
use Src\Core\Controller;
use Src\Utils\Helpers;
use Src\Utils\Html;
use Src\Utils\HttpSocket;

class Reports extends Controller
{
    public function index(): void
    {
        $reportsModel = new ReportsModel();

        $data = [
            "title" => "Relatórios",
            "reports" => $reportsModel->getAllByCategories()
        ];

        $this->renderView("/pages/reports/index", $data);
    }

    private function requestPrinterStatsPage($ip): ?string
    {
        $httpSocket = new HttpSocket($ip, 8080);
        if($httpSocket->getError() !== "success") {
            $httpSocket->close();
            return null;
        }
        $html = $httpSocket->doRequest("get", "/statics.html");
        $httpSocket->close();

        return $html;
    }

    public function getPrinterData($printerIp): void
    {
        header("Content-type: application/json");

        $html = $this->requestPrinterStatsPage($printerIp);

        if(empty($html)) {
            echo json_encode([
               "success" => false
            ]);
            return;
        }

        $printerStatsHtml = new Html($html);

        $bodyPath = "/html/body";
        preg_match("/[tT]oner\s+(\d+)%/", $printerStatsHtml->query($bodyPath)[0]->textContent, $matches);
        $tonerLevel = str_replace(["%", "OK"], ["", "100"], $matches[1]);

        preg_match("/[tT]otal\s+(\d+)/", $printerStatsHtml->query($bodyPath)[0]->textContent, $matches);
        $totalPrints = $matches[1];

        echo json_encode([
            "success" => true,
            "tonerLevel" => $tonerLevel,
            "todayPrints" => "N/A",
            "totalPrints" => $totalPrints
        ]);
    }

    public function printers(): void
    {
        $data = [
            "title" => "Estatísticas das impressoras",
            "printers" => Helpers::getJsonFileData("printers")
        ];

        $this->renderView("/pages/reports/printers/index", $data);
    }
}