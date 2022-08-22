<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Invoice extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['invoice_number','invoice_date','due_date','product_id','section_id','amount_collection','amount_commission','discount','value_vat','rate_vat','total','status','value_status','note','payment_date'];

    public function section(){
        return $this->belongsTo(Section::class);
    }
    public function product(){
        return $this->belongsTo(Product::class);
    }
    public function details(){
        return $this->hasMany(InvoiceDetails::class);
    }
    public function attachments(){
        return $this->hasMany(InvoiceAttachments::class);
    }

    protected static function booted(){
        static::updating(function(Invoice $invoice){
            if($invoice->status=="مدفوعة"){
                $invoice->value_status=1;
            }else{
                $invoice->value_status=3;
            }
        });
    }
}
