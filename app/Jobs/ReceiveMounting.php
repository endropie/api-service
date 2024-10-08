<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ReceiveOrderItem;
use Illuminate\Http\Request;

class ReceiveMounting extends Job
{

    public function __construct($request)
    {
        $this->setQueueRequest($request);
    }

    public function handle()
    {
        $record = ReceiveOrderItem::findOrFail($this->request->get('id'));
        $data = collect($this->request->get('mounts'))->select(['locker_id', 'amount']);

        if ($data->sum('amount') != $record->amount) {
            abort(406, 'The mounting amount does not match');
        }

        app('db')->beginTransaction();

        $record->mounts()->delete();

        $data->each(function($e) use ($record) {
            $mount = $record->mounts()->create($e);

            $mount->locker->mounting($record, $mount->amount, $record->receive_order_id);
        });

        // $record->product->instock($record->amount);

        \App\Events\RecordSaved::dispatchUnconsole($this->qid, $record); //->withDelay(now()->addSeconds(10));

        app('db')->commit();

        return $record;
    }

}
