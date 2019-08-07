<?php
namespace App\Library\VO;

use Illuminate\Support\Facades\Log;

class ResponseJSON
{
    public $success;
    public $description;
    public $recordsTotal;

    public function __construct($success, $description, $recordsTotal){
        Log::info("[ResponseJSON][constructor]");
        $this->success = $success;
        $this->description = $description;
        $this->recordsTotal = $recordsTotal;

    }

    public function getSuccess(){
        Log::info("[ResponseJSON][getSuccess]");
        return $this->success;
    }
}
