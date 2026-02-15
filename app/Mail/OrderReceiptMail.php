<?php
 

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $order;
    public array $orderItems;
    public array $orderUser;
    public array $orderAddress;
    public array $orderBranch;
    public array $setting;
    public array $orderTypeEnum;
    public array $orderTypeEnumArray;
    public array $paymentTypeEnumArray;
    public array $posPaymentMethodEnumArray;
    public array $sourceEnum;
    public string $direction;
    public ?array $transaction = null;

    /**
     * Create a new message instance.
     */
    public function __construct(
        array $order,
        array $orderItems,
        array $orderUser,
        array $orderAddress,
        array $orderBranch,
        array $setting,
        array $orderTypeEnum,
        array $orderTypeEnumArray,
        array $paymentTypeEnumArray,
        array $posPaymentMethodEnumArray,
        array $sourceEnum,
        string $direction = 'ltr',
        // Initialize transaction as null           
          array $transaction = null    


    ) {
        $this->order = $order;
        $this->orderItems = $orderItems;
        $this->orderUser = $orderUser;
        $this->orderAddress = $orderAddress;
        $this->orderBranch = $orderBranch;
        $this->setting = $setting;
        $this->orderTypeEnum = $orderTypeEnum;
        $this->orderTypeEnumArray = $orderTypeEnumArray;
        $this->paymentTypeEnumArray = $paymentTypeEnumArray;
        $this->posPaymentMethodEnumArray = $posPaymentMethodEnumArray;
        $this->sourceEnum = $sourceEnum;
        $this->direction = $direction;  

                $this->transaction = $transaction; 

        // Initialize transaction as null

        }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your Order Receipt')
            ->view('emails.order_receipt')
            ->with([
                'order' => $this->order,
                'orderItems' => $this->orderItems,
                'orderUser' => $this->orderUser,
                'orderAddress' => $this->orderAddress,
                'orderBranch' => $this->orderBranch,
                'setting' => $this->setting,
                'orderTypeEnum' => $this->orderTypeEnum,
                'orderTypeEnumArray' => $this->orderTypeEnumArray,
                'paymentTypeEnumArray' => $this->paymentTypeEnumArray,
                'posPaymentMethodEnumArray' => $this->posPaymentMethodEnumArray,
                'sourceEnum' => $this->sourceEnum,
                'direction' => $this->direction,
                'transaction' => $this->transaction,
            ]);
    }
}