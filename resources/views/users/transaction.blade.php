@extends("layouts.app")
@section('content')

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="mb-2">
            <h1 class="m-0">Transactions</h1>
        </div>
    </div>
</div>

<!-- Main content -->
<div class="content">
    <div class="container-fluid">
        <div class="card">
            
            <!-- /.card-header -->
            <div class="card-body table-responsive p-0">
                <table class="table table-striped text-nowrap">
                    <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>User Wallet</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $key => $trx)
                            <tr>
                                <td>{{$trx->id}}</td>
                                <td>{{$trx->wallet}}</td>
                                <td>{{$trx->amount}}</td>
                                <td> <span class="badge badge-info">{{$trx->status}}</span></td>
                                <td>{{$trx->created_at->diffForhumans()}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.container-fluid -->
</div>
<!-- /.content -->
@endsection