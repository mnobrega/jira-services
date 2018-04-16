@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Wrapper - Activity Report</div>
                    <div class="panel-body">
                        <table class="table">
                            <thead>
                            <th>Issue Key</th>
                            <th>Summary</th>
                            <th>Time Spent (days)</th>
                            <th>Time Spent (percentage)</th>
                            </thead>
                            <tbody>
                            <tr>
                                <td>Key</td>
                                <td>Summary</td>
                                <td>10</td>
                                <td>5%</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection