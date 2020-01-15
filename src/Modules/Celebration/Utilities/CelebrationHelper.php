<?php

namespace OlaHub\UserPortal\Helpers;

class CelebrationHelper extends OlaHubCommonHelper {

    function saveCelebrationCart($celebration, $requestData = false) {
        
        (new \OlaHub\UserPortal\Helpers\LogHelper)->setActionsData(["action_name" => "Save celebration in cart", "action_startData" => $celebration. $requestData]);
        $cart = (new \OlaHub\UserPortal\Helpers\CartHelper)->setCelebrationCartData($celebration, $requestData);
        $totalPrice = \OlaHub\UserPortal\Models\Cart::getCartSubTotal($cart, false);
        if ($totalPrice >= 0) {
            $celebrationCart = \OlaHub\UserPortal\Models\CelebrationModel::where('id', $cart->celebration_id)->first();
            $participants = \OlaHub\UserPortal\Models\CelebrationParticipantsModel::where('celebration_id', $celebrationCart->id)->get();
            $price = $totalPrice / count($participants);
            $celebrationCart->participant_count = count($participants);
            $celebrationCart->save();
            foreach ($participants as $participant) {
                $participant->amount_to_pay = $price;
                $participant->save();
            }
        }
    }

}
