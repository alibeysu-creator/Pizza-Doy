   <div style="font-family: monospace; font-size: 14px; max-width: 190px; margin: 0 auto; padding: 0; font-weight: bold;">


     <!-- HEADER -->
     <div style="text-align: center; margin-bottom: 4px;">
       <div style="font-size: 14px; font-weight: bold;">{{ $setting['company_name'] ?? 'Pizza Doy StadtJoh' }}</div>
       <div>{{ $orderBranch['address'] ?? '' }}</div>
       <div>Tel: {{ $orderBranch['phone'] ?? '' }}</div>
     </div>

     <!-- LIEFERUNG -->
     <div style="border-top: 1px dashed #000; padding: 4px 0; text-align: center;">
       <div>Bestellzeit: {{ $order['order_time'] ?? '' }}</div>
       <div style="margin-top: 4px; font-weight: bold;"># {{ $order['order_serial_no'] ?? '' }}</div>
       <div style="margin-top: 8px;">
         <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data={{ urlencode($order['order_serial_no'] ?? '') }}" alt="QR Code" />
       </div>
     </div>



     <!-- ARTIKEL -->
     <div style="border-top: 1px dashed #000; padding: 4px 0;">
       <div style="text-align: center; font-weight: bold;">ARTIKEL</div>
       @foreach($orderItems as $item)
       <div>{{ $item['quantity'] ?? 1 }} x {{ $item['item_name'] }}</div>
       <div style="text-align: right;">{{ $item['total_without_tax_currency_price'] }} </div>

       @if(!empty($item['item_variations']))
       @foreach($item['item_variations'] as $variation)
       <div style="font-size: 13px;font-weight: bold;">{{ $variation['variation_name'] }}: {{ $variation['name'] }}@if(!$loop->last), @endif</div>
       @endforeach
       @endif


       @if(!empty($item['item_extras']))
       @foreach($item['item_extras'] as $extra)
       <div style="font-size: 13px;font-weight: bold;">+ {{ $extra['name'] }}</div>
       @endforeach
       @endif
       @if(!empty($item['instruction']))
       <div style="font-size: 13px;font-weight: bold;">Hinweis: {{ $item['instruction'] }}</div>
       @endif
       <br>
       @endforeach
     </div>

     <!-- BESTELLSUMME -->
     <div style="border-top: 1px dashed #000; padding: 4px 0;">
       <div style="text-align: center; font-weight: bold;">BESTELLSUMME</div>
       <div>Bestellung: {{ $order['subtotal_currency_price'] ?? '0.00 €' }}</div>
       <div>Liefergebühr: {{ $order['delivery_charge_currency_price'] ?? '0.00 €' }}</div>
       <div style="font-weight: bold;">Gesamt: {{ $order['total_currency_price'] ?? '0.00 €' }}</div>
     </div>

     <!-- BESTELLART -->
     <div style="border-top: 1px dashed #000; padding: 4px 0;">
       <div style="text-align: center; font-weight: bold;">BESTELLART</div>
       <div>
         {{ $orderTypeEnumArray[$order['order_type'] ?? 10] ?? '' }}
         @if(($order['order_type'] ?? null) == ($orderTypeEnum['DELIVERY'] ?? 5) || ($order['order_type'] ?? null) == ($orderTypeEnum['TAKEAWAY'] ?? 10))
         um {{ $order['delivery_time'] ?? '' }}
         @elseif(($order['order_type'] ?? null) == ($orderTypeEnum['DINING_TABLE'] ?? 3) && !empty($order['dining_table']['name']))
         - Tisch: {{ $order['dining_table']['name'] }}
         @endif
       </div>
     </div>

     <!-- ZAHLUNG -->
     <div style="border-top: 1px dashed #000; padding: 4px 0;">
       <div style="text-align: center; font-weight: bold;">ZAHLUNG</div>

       @if(isset($transaction['payment_method']) && !empty($transaction['payment_method']))
       <div>
         Bezahlt von: {{ $transaction['payment_method'] == 'Cash on Delivery' ? 'Bar Zahlung' : $transaction['payment_method'] }} </div>

       @else
       <div>
         Bezahlt von: {{ ($paymentTypeEnumArray[$order['payment_method']] ?? '') == 'Cash on Delivery' ? 'Bar Zahlung' : ($paymentTypeEnumArray[$order['payment_method']] ?? 'Unbekannt') }} </div>
       @endif

     </div>


     <!-- KUNDENINFORMATION -->
     <div style="border-top: 1px dashed #000; padding: 4px 0;">
       <div style="text-align: center; font-weight: bold;">KUNDENINFORMATION</div>
       <div>Name: {{ $orderUser['name'] ?? '' }}</div>
       <div>Tel: {{ ($orderUser['country_code'] ?? '') . ($orderUser['phone'] ?? '') }}</div>
       @if(($order['order_type'] ?? '') === ($orderTypeEnum['DELIVERY'] ?? 5))
       <div>
         Adresse:<br>
         {{ $orderAddress['apartment'] ?? '' }} {{ $orderAddress['address'] ?? '' }}<br>
         {{ $orderAddress['postal_code'] ?? '' }} {{ $orderAddress['city'] ?? '' }}
       </div>
       @endif
     </div>

     <!-- ZEIT -->
     <div style="border-top: 1px dashed #000; padding: 4px 0;">
       <div style="text-align: center; font-weight: bold;">ZEIT</div>
       <div>Bestellt am: {{ $order['order_date'] ?? '' }} {{ $order['order_time'] ?? '' }}</div>
     </div>

     <!-- FOOTER -->
     <div style="text-align: center; border-top: 1px dashed #000; font-size: 9px; padding: 6px 0;">
       Das ist keine Rechnung<br>
       © {{ date('Y') }} {{ $setting['company_name'] ?? 'Pizza Doy StadtJoh' }}
     </div>

   </div>