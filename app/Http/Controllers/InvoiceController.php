<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceAttachments;
use App\Models\InvoiceDetails;
use App\Models\Product;
use App\Models\Section;
use App\Notifications\AddInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use App\Exports\InvoicesExport;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoices=Invoice::with(['section','product'])->get();
        return view('invoices.invoices',compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
        $sections=Section::all();
        return view('invoices.create',compact(['sections']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $image_path=null;
        if($request->hasFile('pic')){
            $image_path = $request->file('pic')->store($request->invoice_number, [
                'disk' => 'public',
            ]);
        }
        $invoice=Invoice::create($request->all());
        $request->merge([
            'invoice_id'=>$invoice->id,
            'image'=>$image_path
        ]);
        InvoiceDetails::create($request->all());
        InvoiceAttachments::create($request->all());
        $users=User::get();

            Notification::send($users,new AddInvoice($invoice));
        
        return back()->with('success','تم اضافة الفاتورة بنجاح');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        $invoice::with(['section','product','attachments.user','details.user'])->first();
        return view('invoices.show',compact('invoice'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function edit(Invoice $invoice)
    {
        $invoice::with(['section','product'])->first();
        $sections=Section::all();
        return view('invoices.edit',compact('invoice','sections'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoice $invoice)
    {
        $invoice->update($request->all());
        return back()->with('success','تم تعديل الفاتورة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $invoice=Invoice::findOrFail($request->invoice_id);
        $invoice->delete();
        return back()->with('success','تم نقل الفاتورة الى الأرشيف');
    }


    public function forceDelete(Request $request)
    {
        $invoice=Invoice::withTrashed()->findOrFail($request->invoice_id);
        Storage::disk('public')->deleteDirectory($invoice->invoice_number);
        $invoice->forceDelete();
        return back()->with('success','تم حذف الفاتورة بنجاح');
    }
    
    public function archive()
    {
        $invoices=Invoice::onlyTrashed()->with(['section','product'])->get();
        return view('invoices.archive',compact('invoices'));
    }

    public function restore(Request $request)
    {
        $invoice=Invoice::onlyTrashed()->findOrFail($request->invoice_id);
        $invoice->restore();
        return redirect()->route('invoices.index')->with('restore','تم استعادة الفاتورة بنجاح');
    }

    public function statusShow($invoice){
        $invoice=Invoice::with(['section','product'])->findOrFail($invoice);
        return view('invoices.updateStatus',compact('invoice'));
    }
    public function statusUpdate(Request $request){
        $request->validate([
            'status'=>'required',
            'payment_date'=>'required',
        ]);
        if($request->status=="مدفوعة"){
            $value_status=1;
        }else{
            $value_status=3;
        }
        $request->merge([
            'value_status'=>$value_status,
            'user_id'=>auth()->id()
        ]);
        $invoice=Invoice::findOrFail($request->invoice_id);
        $invoice->update($request->all());
        InvoiceDetails::create($request->all());
        return redirect()->route('invoices.show',$invoice->id)->with('success_change','sss');
    }

    public function paid(){
        $invoices=Invoice::with(['section','product'])->where('value_status',1)->get();
        return view('invoices.paid',compact('invoices'));
    }
    public function unpaid(){
        $invoices=Invoice::with(['section','product'])->where('value_status',2)->get();
        return view('invoices.unpaid',compact('invoices'));
    }
    public function part(){
        $invoices=Invoice::with(['section','product'])->where('value_status',3)->get();
        return view('invoices.part',compact('invoices'));
    }
    public function print($id){
        $invoice=Invoice::with(['section','product'])->findOrFail($id);
        return view('invoices.print',compact('invoice'));
    }

    public function productsOfSection(Request $request)
    {
        return $products=Product::where('section_id',$request->id)->get(); 
    }

    public function export() 
    {
        return Excel::download(new InvoicesExport, 'invoices.xlsx');
    }

    public function showReport(){
        return view('reports.invoices');
    }
    public function search(Request $request){

        if($request->rdio==1){

            if($request->type && $request->start_at=="" && $request->end_at==""){
                $invoices=Invoice::with(['details','product','section'])->whereStatus($request->type)->get();
                $type=$request->type;
                return view('reports.invoices',compact('invoices','type'));
            }else{
                 $start_at = date($request->start_at);
                $end_at = date($request->end_at);
                $type = $request->type;
                $invoices=Invoice::with(['details','product','section'])->whereBetween('invoice_date',[$start_at,$end_at])->whereStatus($request->type)->get();
                return view('reports.invoices',compact('type','start_at','end_at','invoices'));
            }
        }else{
            $invoices=Invoice::with(['details','product','section'])->where('invoice_number',$request->invoice_number)->get();
                return view('reports.invoices',compact('invoices'));
        }
    }
}
