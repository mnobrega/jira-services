@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Jira Wrapper Status</div>
                    <div class="panel-body">
                        <span>Número de Issues Sincronizadas: {{$featureResult['issuesCount']}}</span><br />
                        <span>Última Issue Sincronizada: {{$featureResult['latestUpdatedIssue'][0]->key}}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection