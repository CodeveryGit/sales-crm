@extends('layouts.app')

@section('content')
    {{ Breadcrumbs::render('lead-archive', $lead) }}

    <div id="leads-show" class="container">
        <div class="leads-header">

            <form action="{{ route('lead.from-archive', $lead->id) }}" method="POST">
                {{ csrf_field() }}
                <button type="submit" class="button button-sky-blue">
                    {{trans_choice('common.from',1)}}
                    {{trans_choice('common.archive',2)}}
                </button>
            </form>

            @can('destroy', $lead)
                <delete-model :route-link="'leads'" model-id="{{$lead->id}}" :model-name="'leads'"></delete-model>
            @endcan
        </div>

        <div class="lead-info card-border">
            <div class="lead-name">
                <h1>{{ $lead->name }}</h1>
                <a href="{{ $lead->url }}">{{ $lead->url }}</a>
            </div>
            <hr>


            <div class="lead-statuses">
                <div class="status passed">
                    <div class="status-name">{{ $status->name }}</div>
                    <div class="status-triangle"></div>
                </div>
            </div>


            <div class="basic-info">
                <div class="lead-card half-block card-border mr-4">
                    <div class="lead-block">
                        <div class="block-label label-gray">{{trans_choice('common.type', 1)}}:</div>
                        <div class="block-value">{{ $lead->lead_type_name }}</div>
                    </div>
                    @if(isset($lead->client))
                        <div class="lead-block">
                            <div class="block-label label-gray">{{trans_choice('models.clients', 1)}}:</div>
                            <div class="block-value">{{ $lead->client->name }}</div>
                        </div>
                    @endif
                    <div class="lead-block">
                        <div class="block-label label-gray">{{trans_choice('models.managers', 1)}}:</div>
                        <div class="block-value">{{ $lead->manager->name }}</div>
                    </div>
                </div>
                <div class="lead-card half-block card-border">
                    <div class="lead-block">
                        <div class="block-label label-gray">{{trans_choice('common.priority', 1)}}:</div>
                        <div class="block-value priority">{{ trans_choice('common.'.$lead->priority, 1) }}</div>
                    </div>
                    <div class="lead-block">
                        <div class="block-label label-gray">{{trans_choice('common.priority', 1)}}:</div>
                        <div class="block-value">{{ $lead->payment_quantity }}</div>
                    </div>
                    <div class="lead-block">
                        <div class="block-label label-gray">{{trans_choice('common.comment', 1)}}:</div>
                        <div class="block-value">{{ $lead->comment }}</div>
                    </div>
                </div>
            </div>


            @if(count($lead->fields) != 0)
                <div class="lead-card card-border">
                    <div class="lead-fields">
                        @foreach($lead->fields as $field)
                            <div class="lead-block">
                                <div class="block-label label-gray">{{ $field->name }}</div>
                                <div class="block-value mr-3">{{ $field->pivot->value }}</div>
                            </div>
                        @endforeach()
                    </div>
                </div>
            @endif
        </div>


        <div>
            <div class="lead-history card-border">
                <div class="history-header">
                    <h3 class="history-title">{{trans_choice('common.history', 1)}}</h3>
                </div>
                <hr>
                <div class="history-logs">
                    @foreach($lead->logs as $log)
                        <div class="history-log">
                            <div class="log-circle"></div>
                            <div class="log-block card-border">
                                <div class="log-name">{{ $log->status_name }}</div>
                                <div class="log-comment">{{ $log->comment ===  'Lead created' ? trans_choice('leads.lead_created', 1) : $log->comment}}</div>
                                <div class="log-date label-gray">
                                    <i class="icon-history-date"></i>
                                    <span>{{ $log->created_at }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
