<?php


namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\User;
use App\Models\Order;
use App\Services\OrderService;
use App\Http\Resources\OrderDetailsResource;
use App\Mail\OrderReceiptMail;
use Illuminate\Support\Facades\Mail;
use App\Enums\Role;
use App\Models\NotificationAlert;

use App\Enums\SwitchBox;
use App\Mail\OrderGotMail;
use App\Models\FrontendOrder;
use Illuminate\Support\Facades\Log;

class MyOrderDetailsController extends AdminController
{

    private OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        parent::__construct();
        $this->orderService = $orderService;
    }

    public function orderDetails(User $user, Order $order): \Illuminate\Http\Response | OrderDetailsResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new OrderDetailsResource($this->orderService->orderDetails($user, $order));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function sendOrderReceipt($orderId): \Illuminate\Http\Response | OrderDetailsResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {

        try {
            // Initialize variables         

            // Find the order by ID
            $order = Order::findOrFail($orderId)
                ->load('user', 'address', 'branch', 'deliveryBoy', 'coupon', 'diningTable', 'orderItems.orderItem');



            $orderDetailsResource = new OrderDetailsResource($this->orderService->show($order, false));
            $orderDetails = $orderDetailsResource->toArray(request());
            // Prepare variables for the Blade (adjust keys as needed)
            $orderArr = $orderDetails;
            // dd( $orderArr);
            $orderItems = $orderDetails['order_items'] ?? [];
            if (!is_array($orderItems)) {
                $orderItems = json_decode(json_encode($orderItems), true) ?: [];
            }





            $orderUser =
                $orderDetails['user'] ?? [];
            if (!is_array($orderUser)) {
                $orderUser = json_decode(json_encode($orderUser), true) ?: [];
            }
            /*   
        $orderAddress = $orderDetails['order_address'] ?? [];
        if (!is_array($orderAddress)) {
            $orderAddress = json_decode(json_encode($orderAddress), true) ?: [];
        } */
            $orderAddress = [];

            if (($orderDetails['order_type'] ?? null) != 10) {
                $orderAddress = $orderDetails['order_address'] ?? [];

                if (!is_array($orderAddress)) {
                    $orderAddress = json_decode(json_encode($orderAddress), true) ?: [];
                }
            }






            $orderBranch = $orderDetails['branch'] ?? [];
            if (!is_array($orderBranch)) {
                $orderBranch = json_decode(json_encode($orderBranch), true) ?: [];
            }

            $setting = $orderDetails['setting'] ?? ['company_name' => config('app.name')];
            if (!is_array($setting)) {
                $setting = json_decode(json_encode($setting), true) ?: [];
            }




            if ($orderDetails['transaction'] && $orderDetails['transaction']->resource) {
                $transaction =
                    json_decode(json_encode($orderDetails['transaction']), true);
            } else {

                $transaction = [];
            }
            // Define enums and arrays for order types, payment methods, etc.




            $orderTypeEnum = [
                'DELIVERY' => 5,
                'TAKEAWAY' => 10,
                'DINING_TABLE' => 3,
            ];

            $orderTypeEnumArray = [
                5 => 'Lieferung',
                10 => 'Abholung',
                3 => 'Im Restaurant',
            ];

            $paymentTypeEnumArray = [
                '1' => 'Cash on Delivery',
                '2' => 'Online Payment',
                '3' => 'Wallet Payment',
                // ...add more as needed
            ];
            $posPaymentMethodEnumArray = [
                'cash' => 'Cash',
                'card' => 'Card',
                // ...add more as needed
            ];
            $sourceEnum = [
                'POS' => 1,
                // ...add more as needed
            ];

            $direction = 'ltr'; // or 'rtl' as needed
            //dd($orderArr);
            // Send the email

            if (!blank($order)) {
                $emailAllAdmins = User::role(Role::ADMIN)->where(['branch_id' => 0])->whereNotNull('email')->get();
                $emailBranchAdmins = User::role(Role::ADMIN)->where(['branch_id' => $order->branch_id])->whereNotNull('email')->get();
                $emailBranchManagers = User::role(Role::BRANCH_MANAGER)->where(['branch_id' => $order->branch_id])->whereNotNull('email')->get();

                // Log::info( 'Sending order receipt email to: ' . implode(', ', $emailArray));
                // log::info( 'emailAllAdmins ID: ' . $emailAllAdmins->pluck('id')->implode(', '));
                // log::info( 'emailBranchAdmins ID: ' . $emailBranchAdmins->pluck('id')->implode(', '));
                // log::info( 'emailBranchManagers ID: ' . $emailBranchManagers->pluck;



                // Collect emails from all admins and branch managers


                $i = 0;
                $emailArray = [];
                if (!blank($emailAllAdmins)) {
                    foreach ($emailAllAdmins as $emailAllAdmin) {
                        $emailArray[$i] = $emailAllAdmin->email;
                        $i++;
                    }
                }

                if (!blank($emailBranchAdmins)) {
                    foreach ($emailBranchAdmins as $emailBranchAdmin) {
                        $emailArray[$i] = $emailBranchAdmin->email;
                        $i++;
                    }
                }

                if (!blank($emailBranchManagers)) {
                    foreach ($emailBranchManagers as $emailBranchManager) {
                        $emailArray[$i] = $emailBranchManager->email;
                        $i++;
                    }
                }

                if (count($emailArray) > 0) {
                    try {
                        $notificationAlert = NotificationAlert::where(['language' => 'admin_and_branch_manager_new_order_message'])->first();
                        if ($notificationAlert && $notificationAlert->mail == SwitchBox::ON) {
                            try {
                                Log::info('Sending order receipt email to: ' . implode(', ', $emailArray));




                                Mail::to($emailArray[0])->cc($emailArray)->send(new OrderReceiptMail(

                                    $orderArr,
                                    $orderItems,
                                    $orderUser,
                                    $orderAddress,
                                    $orderBranch,
                                    $setting,
                                    $orderTypeEnum,
                                    $orderTypeEnumArray,
                                    $paymentTypeEnumArray,
                                    $posPaymentMethodEnumArray,
                                    $sourceEnum,
                                    $direction,
                                    $transaction,

                                ));

                                return response(['status' => true, 'message' => 'Order receipt sent successfully.']);
                            } catch (Exception $e) {
                                Log::info($e->getMessage());
                            }
                        }
                    } catch (Exception $e) {
                        Log::info($e->getMessage());
                    }
                }
            }


            // Mail::to($orderUser['email'])->send(new OrderReceiptMail(

            //     $orderArr,
            //     $orderItems,
            //     $orderUser,
            //     $orderAddress,
            //     $orderBranch,
            //     $setting,
            //     $orderTypeEnum,
            //     $orderTypeEnumArray,
            //     $paymentTypeEnumArray,
            //     $posPaymentMethodEnumArray,
            //     $sourceEnum,
            //     $direction
            // ));

            return response(['status' => true, 'message' => 'Order receipt sent successfully.']);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
