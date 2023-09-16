<?php

namespace Src\App\Controllers;

use Src\Core\Controller;
use Src\App\Models\BirthdayPeopleModel;
use Src\App\Models\UnitsPhonesModel;

class Home extends Controller
{
    public function index(): void
    {
        $birthdayPeopleModel = new BirthdayPeopleModel();
        $unitsPhonesModel = new UnitsPhonesModel();

        $data = [
            "title" => "Portal Interno Filial 273",
            "birthdayPeople" => $birthdayPeopleModel->getAll(),
            "unitPhones" => $unitsPhonesModel->getAll([
                "where" => [
                    "comparison" => "unitId =",
                    "value" => CONF_DEFAULT_UNIT_ID
                ]
            ])
        ];

        $this->renderView("/pages/home/index", $data);
    }
}
