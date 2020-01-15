<?php

namespace OlaHub\UserPortal\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

class OlaHubLandingPageController extends BaseController {

    protected $requestData;
    protected $requestFilter;
    protected $userAgent;

    public function __construct(Request $request) {
        $return = \OlaHub\UserPortal\Helpers\OlaHubCommonHelper::getRequest($request);
        $this->requestData = $return['requestData'];
        $this->requestFilter = $return['requestFilter'];
        $this->userAgent = $request->header('uniquenum') ? $request->header('uniquenum') : $request->header('user-agent');
    }

    public function getTrendingData() {
        $log = new \OlaHub\UserPortal\Helpers\LogHelper();
        $log->setLogSessionData(['module_name' => "Items", 'function_name' => "getTrendingData"]);
       
        $itemModel = (new \OlaHub\UserPortal\Models\CatalogItem)->newQuery();
        $itemModel->where(function ($query) {
            $query->whereNull('parent_item_id');
            $query->orWhere('parent_item_id', '0');
        });
        $itemModel->orderBy('total_views', 'DESC');
        $itemModel->orderBy('name', 'ASC');
        $itemModel->take(15);
        $items = $itemModel->get();
        if ($items->count() < 1) {
            throw new NotAcceptableHttpException(404);
        }
        $return = \OlaHub\UserPortal\Helpers\CommonHelper::handlingResponseCollection($items, '\OlaHub\UserPortal\ResponseHandlers\ItemsListResponseHandler');
        $return['status'] = true;
        $return['code'] = 200;
        $log->setLogSessionData(['response' => $return]);
        $log->saveLogSessionData();
        return response($return, 200);
    }

    public function getMostOfferData() {
        $log = new \OlaHub\UserPortal\Helpers\LogHelper();
        $log->setLogSessionData(['module_name' => "Items", 'function_name' => "getMostOfferData"]);
       
        $itemModel = (new \OlaHub\UserPortal\Models\CatalogItem)->newQuery();
        $itemModel->selectRaw('*, ((discounted_price / price) * 100) as discount_perc');
        
        $itemModel->where(function ($query) {
            $query->whereNotNull('discounted_price_start_date');
            $query->whereNotNull('discounted_price_end_date');
            $query->where('discounted_price_start_date', '<=', date('Y-m-d') . " 00:00:01");
            $query->where('discounted_price_end_date', '>=', date('Y-m-d') . " 23:59:59");
        });
        $itemModel->orderByRaw("RAND()");
        $itemModel->take(15);
        $items = $itemModel->get();
        if ($items->count() < 1) {
            throw new NotAcceptableHttpException(404);
        }
        $return = \OlaHub\UserPortal\Helpers\CommonHelper::handlingResponseCollection($items, '\OlaHub\UserPortal\ResponseHandlers\ItemsListResponseHandler');
        $return['status'] = true;
        $return['code'] = 200;
        $log->setLogSessionData(['response' => $return]);
        $log->saveLogSessionData();
        return response($return, 200);
    }

    public function getOccasionsData() {
        $log = new \OlaHub\UserPortal\Helpers\LogHelper();
        $log->setLogSessionData(['module_name' => "Items", 'function_name' => "getOccasionsData"]);
       
        $occasions = \OlaHub\UserPortal\Models\Occasion::items()
                ->orderBy("order_occasion", "ASC")
                ->orderBy("name", "ASC")
                ->get();
        if ($occasions->count() < 1) {
            throw new NotAcceptableHttpException(404);
        }
        $return = \OlaHub\UserPortal\Helpers\CommonHelper::handlingResponseCollection($occasions, '\OlaHub\UserPortal\ResponseHandlers\OccasionsHomeResponseHandler');
        unset($occasions);
        $return['status'] = true;
        $return['code'] = 200;
        $log->setLogSessionData(['response' => $return]);
        $log->saveLogSessionData();
        return response($return, 200);
    }

    public function getInterestsData() {
        $log = new \OlaHub\UserPortal\Helpers\LogHelper();
        $log->setLogSessionData(['module_name' => "Items", 'function_name' => "getInterestsData"]);
       
        $interests = \OlaHub\UserPortal\Models\Interests::has('itemsRelation')->orderBy('name', 'ASC')->get();
        if ($interests->count() < 1) {
            throw new NotAcceptableHttpException(404);
        }
        $return = \OlaHub\UserPortal\Helpers\CommonHelper::handlingResponseCollection($interests, '\OlaHub\UserPortal\ResponseHandlers\InterestsHomeResponseHandler');
        unset($interests);
        $return['status'] = true;
        $return['code'] = 200;
        $log->setLogSessionData(['response' => $return]);
        $log->saveLogSessionData();
        return response($return, 200);
    }

    public function getBrandsData() {
        $log = new \OlaHub\UserPortal\Helpers\LogHelper();
        $log->setLogSessionData(['module_name' => "Items", 'function_name' => "getBrandsData"]);
       
        $brandsModel = (new \OlaHub\UserPortal\Models\Brand)->newQuery();
        $brandsModel->whereHas('itemsMainData', function($query) {
            $query->where('is_published', '1');
        });
        $brands = $brandsModel->get();
        if ($brands->count() < 1) {
            throw new NotAcceptableHttpException(404);
        }
        $return = \OlaHub\UserPortal\Helpers\CommonHelper::handlingResponseCollection($brands, '\OlaHub\UserPortal\ResponseHandlers\BrandsResponseHandler');
        unset($brands);
        $return['status'] = true;
        $return['code'] = 200;
        $log->setLogSessionData(['response' => $return]);
        $log->saveLogSessionData();
        return response($return, 200);
    }

    /*
     * Helper functions
     */

    private function getClasses($type) {
        $log = new \OlaHub\UserPortal\Helpers\LogHelper();
        $log->setLogSessionData(['module_name' => "Items", 'function_name' => "getClasses"]);
       
        $classesMainModel = (new \OlaHub\UserPortal\Models\Classification)->newQuery();
//        if (count($this->requestFilter) > 0) {
//            foreach ($this->requestFilter as $input => $value) {
//                $classesMainModel->where(\OlaHub\UserPortal\Helpers\CommonHelper::getColumnName(\OlaHub\UserPortal\Models\Classification::$columnsMaping, $input), $value);
//            }
//            unset($value, $input);
//        }
        $classesMainModel->where('is_main', $type);
        $classesMainModel->whereHas('itemsMainData', function($query) {
            $query->where('is_published', '1');
        });
        return $classesMainModel->get();
    }

}
