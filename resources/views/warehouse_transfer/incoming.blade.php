@foreach($requests as $req)
<h4>{{ $req->request_no }}</h4>

<table border="1">
@foreach($req->items as $item)
<tr>
    <td>{{ $item->product->name }}</td>
    <td>{{ $item->requested_qty }}</td>
</tr>
@endforeach
</table>

<form method="POST" action="{{ url('warehouse-transfer-request/approve/'.$req->id) }}">
@csrf
<button>Approve</button>
</form>

<form method="POST" action="{{ url('warehouse-transfer-request/reject/'.$req->id) }}">
@csrf
<button>Reject</button>
</form>
<hr>
@endforeach
