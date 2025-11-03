<?php
use App\Models\Company;

if(!function_exists('getCompany')){
    function getCompany(){
        return Company::findorFail(session('company_id'));
    }
}
