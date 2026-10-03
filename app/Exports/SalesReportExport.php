<?php

namespace App\Exports;

use App\Enums\OrderType;
use App\Services\OrderService;
use App\Http\Requests\PaginateRequest;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class SalesReportExport implements FromCollection, WithHeadings
{
    public OrderService $orderService;
    public PaginateRequest $request;

    public function __construct(OrderService $orderService, $request)
    {
        $this->orderService = $orderService;
        $this->request = $request;
    }

    public function collection(): \Illuminate\Support\Collection
    {
        $rows = [];
        $orders = $this->orderService->list($this->request);

        foreach ($orders as $order) {
            $gross7 = 0;
            $gross19 = 0;

            if ($order->orderItems) {
                foreach ($order->orderItems as $item) {
                    $itemTotal = (float) $item->total_price;
                    $taxRate = (float) $item->tax_rate;

                    if ($taxRate == 7.0) {
                        $gross7 += $itemTotal;
                    }

                    if ($taxRate == 19.0) {
                        $gross19 += $itemTotal;
                    }
                }
            }

            $deliveryCharge = (float) $order->delivery_charge;
            $discount = (float) $order->discount;

            // Liefergeb«ähr immer zu 7%
            $gross7 += $deliveryCharge;

            // Rabatt als Minus bei 7%
            $gross7 -= $discount;

            // Falls 7%-Betrag negativ wird, auf 0 setzen
            if ($gross7 < 0) {
                $gross7 = 0;
            }

            $date = date('d.m.Y', strtotime($order->order_datetime));

            $paymentName = $order->transaction
                ? strtoupper($order->transaction->payment_method)
                : $this->getPaymentMethod($order);

            $invoiceNumber = $order->order_serial_no;

            // 7%-Zeile mit Buchungskonto 8300
            if (round($gross7, 2) > 0) {
                $rows[] = [
                    $date,
                    round($gross7, 2),
                    8300,
                    $paymentName,
                    $invoiceNumber,
                ];
            }

            // 19%-Zeile mit Buchungskonto 8400
            if (round($gross19, 2) > 0) {
                $rows[] = [
                    $date,
                    round($gross19, 2),
                    8400,
                    $paymentName,
                    $invoiceNumber,
                ];
            }
        }

        return collect($rows);
    }

    public function headings(): array
    {
        return [
            'Erstelldatum',
            'Betrag Brutto',
            'Buchungskonto',
            'Zahlungsname',
            'Rechnungsnummer',
        ];
    }

    public function getPaymentMethod($order)
    {
        if ($order->order_type === OrderType::POS) {
            return trans('pos_payment_method.' . $order->pos_payment_method) != "pos_payment_method."
                ? trans('pos_payment_method.' . $order->pos_payment_method)
                : "";
        }

        return trans('payment_gateway.' . $order->payment_method);
    }
}