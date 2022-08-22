<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard(){
        $invoicesCount=Invoice::count('total');
        $invoicesSum=Invoice::sum('total');

        $paidInvoicesCount=Invoice::where('value_status',1)->count('total');
        $paidInvoicesSum=Invoice::where('value_status',1)->sum('total');

        $unpaidInvoicesCount=Invoice::where('value_status',2)->count('total');
        $unpaidInvoicesSum=Invoice::where('value_status',2)->sum('total');

        $partInvoicesCount=Invoice::where('value_status',3)->count('total');
        $partInvoicesSum=Invoice::where('value_status',3)->sum('total');






      if($unpaidInvoicesCount == 0){
          $nspainvoices2=0;
      }
      else{
          $nspainvoices2 = $unpaidInvoicesCount/ $invoicesCount*100;
      }
        if($paidInvoicesCount == 0){
            $nspainvoices1=0;
        }
        else{
            $nspainvoices1 = $paidInvoicesCount/ $invoicesCount*100;
        }
        if($partInvoicesCount == 0){
            $nspainvoices3=0;
        }
        else{
            $nspainvoices3 = $partInvoicesCount/ $invoicesCount*100;
        }


        $chartjs = app()->chartjs
            ->name('barChartTest')
            ->type('bar')
            ->size(['width' => 350, 'height' => 200])
            ->labels(['الفواتير الغير المدفوعة', 'الفواتير المدفوعة','الفواتير المدفوعة جزئيا'])
            ->datasets([
                [
                    "label" => "الفواتير الغير المدفوعة",
                    'backgroundColor' => ['#ec5858'],
                    'data' => [$nspainvoices2]
                ],
                [
                    "label" => "الفواتير المدفوعة",
                    'backgroundColor' => ['#81b214'],
                    'data' => [$nspainvoices1]
                ],
                [
                    "label" => "الفواتير المدفوعة جزئيا",
                    'backgroundColor' => ['#ff9642'],
                    'data' => [$nspainvoices3]
                ],


            ])
            ->options([]);


            $chartjs_2 = app()->chartjs
            ->name('pieChartTest')
            ->type('pie')
            ->size(['width' => 340, 'height' => 200])
            ->labels(['الفواتير الغير المدفوعة', 'الفواتير المدفوعة','الفواتير المدفوعة جزئيا'])
            ->datasets([
                [
                    'backgroundColor' => ['#ec5858', '#81b214','#ff9642'],
                    'data' => [$nspainvoices2, $nspainvoices1,$nspainvoices3]
                ]
            ])
            ->options([]);


        return view('dashboard',compact('invoicesCount','invoicesSum','paidInvoicesSum','unpaidInvoicesSum','partInvoicesSum','partInvoicesCount','unpaidInvoicesCount','paidInvoicesCount','chartjs','chartjs_2'));
    }

    public function markAsRead(){
        auth()->user()->unreadNotifications->markAsRead();
        return redirect()->back();
    }
}
