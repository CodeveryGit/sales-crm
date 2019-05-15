@extends('layouts.app')

@section('content')
    {{ Breadcrumbs::render('lead', $lead) }}
    <leads-show :data-lead="{{ json_encode($lead) }}" :data-fields="{{ json_encode($intersectingFields) }}" inline-template>
        <div id="leads-show" class="container">
            <div class="leads-header">
                <button type="button" class="button button-purple" data-toggle="modal" data-target="#lead-modal">
                    @lang("common.edit")
                </button>

                @include('leads._modal', ['action' => 'edit'])

                <form action="{{ route('lead.to-archive', $lead->id) }}" method="POST">
                    {{ csrf_field() }}
                    <button type="submit" class="button button-sky-blue ml-3">
                        {{ trans_choice('common.to', 1) }}
                        {{ trans_choice('common.archive', 1) }}
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

                <lead-statuses :data-lead="lead"></lead-statuses>

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
                            <div class="block-label label-gray">{{trans_choice('common.price', 1)}}:</div>
                            <div class="block-value">{{ $lead->payment_quantity }}</div>
                        </div>
                        <div class="lead-block">
                            <div class="block-label label-gray">{{trans_choice('common.comment', 1)}}:</div>
                            <div class="block-value">{{ $lead->comment }}</div>
                        </div>
                    </div>
                </div>
                <div class="lead-card card-border" v-if="lead.fields.length !== 0">
                    <div class="lead-fields">
                        <div class="lead-block" v-for="(field, i) in allFields">
                            <div class="block-label label-gray" v-text="field.name"></div>
                            <div
                                    v-text="field.value"
                                    v-show="!editing.includes(field)"
                                    class="block-value mr-3"
                            ></div>

                            <input type="text"
                                   v-show="editing.includes(field)"
                                   v-model="field.value"
                                   class="form-control mr-3"
                            >

                            <button
                                    @click="save(field)"
                                    v-show="editing.includes(field)"
                                    class="button button-green button-round button-tick mr-1"

                            >
                            </button>

                            <button
                                    @click="cancel(field)"
                                    v-show="editing.includes(field)"
                                    class="button button-red button-round button-cross"
                            >
                            </button>

                            <button
                                    @click="edit(field)"
                                    v-show="!editing.includes(field)"
                                    class="button button-purple button-round button-pencil mr-1"
                            ></button>
                            {{--<button @click="remove(field, i)"--}}
                                    {{--v-show="!editing.includes(field)"--}}
                                    {{--class="button button-purple button-round button-trash"></button>--}}
                        </div>
                    </div>
                </div>
            </div>
            <model-tasks  :data-tasks="{{ $lead->tasks }}"
                          :data-model-name="{{ collect(['leads', 'lead']) }}"
                          :data-model-id="{{ $lead->id }}"
                          :data-statuses="{{ json_encode(config('constants.TASK_STATUS'))}}"></model-tasks>
            <lead-history :data-lead="lead"></lead-history>
        </div>
    </leads-show>
@endsection
