<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceAttachments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InvoiceAttachmentsController extends Controller
{
    public function add(Request $request){
        if($request->hasFile('pic')){
            $image_path = $request->file('pic')->store('/'.$request->invoice_number, [
                'disk' => 'public',
            ]);
        }
        $request->merge([
            'image'=>$image_path
        ]);
        InvoiceAttachments::create($request->all());
        return back()->with('success','تم اضافة المرفق بنجاح');
    }
    public function delete(Request $request){
        $attachment=InvoiceAttachments::findOrFail($request->id);
        Storage::disk('public')->delete($attachment->image);
        $attachment->delete();
        return back()->with('success','تم حذف المرفق بنجاح');
    }
    public function download($id){
        $attachment=InvoiceAttachments::findOrFail($id);
        return $contents= Storage::disk('public')->getDriver()->getAdapter()->applyPathPrefix($attachment->image);
        return response()->download($contents);
    }
    public function show($id){
        $attachment=InvoiceAttachments::findOrFail($id);
        return response()->file(public_path('storage/' . $attachment->image));
    }
}
