@extends('layouts.app')

@section('content')

    {{ Breadcrumbs::render('leads') }}

    <div id="leads-index">
        <div class="leads-header">
            <button type="button" class="button button-purple" data-toggle="modal" data-target="#lead-modal">
                @lang("common.add")
            </button>


            @if (empty($absentModels))
                @include('leads._modal', ['action' => 'create'])
            @else
                @include('warnings._modal-creation', ['absent_models' => $absentModels, 'modal_name' => 'lead-modal'])
            @endif

            <a href="{{ route('lead.archive-index') }}" class="button button-sky-blue">
                {{trans_choice('common.archive',1)}}
            </a>

            @if(auth()->user()->isHighLevelAccess())
                <a href="{{ route('lead_types.index') }}" class="button button-blue">
                    {{trans_choice('models.lead_types', 2)}}
                </a>
            @endif
        </div>

        <leads-index :data-lead-types="{{ json_encode($leadtypes) }}"
                     :data-statuses="{{ json_encode($leadtypeStatuses) }}"
                     :data-leads="{{ json_encode($leads) }}"
                     inline-template
                     v-cloak>
            <div class="leads-wrapper" v-if="leadtypeSelected">

                <div class="leads-filters">
                    <div class="form-group">
                        <select class="form-control"
                                name="lead_type"
                                @change="filterByLeadtype"
                                v-model="leadtypeSelected">

                            <option v-for="leadtype in leadtypes"
                                    v-text="leadtype.name"
                                    :value="leadtype"
                            ></option>

                        </select>
                    </div>

                    @can('high-level', \Codevery\Lead::class)
                        <div class="form-group ml-3">
                            <select class="form-control"
                                    name="manager"
                                    @change="filterByManager"
                                    v-model="manager">
                                <option value="">@lang('common.all')</option>
                            @foreach($managers as $manager)
                                    <option value="{{ $manager->id }}">{{ $manager->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endcan

                    <div class="form-group ml-3">
                        <daterangepicker @datechanged="filterByDate"></daterangepicker>
                    </div>
                    <div class="form-group ml-3  search-container">
                        <input placeholder="@lang('common.search')" class="form-control search-input" type="text" @keyup="searchLeads" v-model="search">
                    </div>
                </div>

                <div class="leads">

                    <div class="lead-status" v-for="status in statuses">
                        <div class="status">
                            <div class="status-name" v-text="status.name"></div>
                            <div class="status-triangle"></div>
                        </div>
                        {{--TODO: refactor for js filtShow moreer?--}}
                        <div class="leads-container" @scroll="showMoreLeads(status.position)">
                            <div class="lead"
                                 v-for="lead in leads"
                                 v-if="(
                             lead.lead_type_id === leadtypeSelected.id &&
                             lead.current_status === status.position &&
                             (lead.manager_id == manager || manager == '')
                             )"
                            >

                                <a class="lead-name" :href="`/leads/${lead.id}`" v-text="lead.name"></a>
                                <div class="lead-block">
                                    <div class="block-label label-gray" v-text="$t('common.pricing_structure') +':'"></div>
                                    <div class="block-value"  v-text="$t(`common.${lead.payment_type}`)"></div>
                                </div>
                                <div class="lead-block">
                                    <div class="block-label label-gray" v-text="$t('common.priority') +':'"></div>
                                    <div class="block-value priority"
                                         :class="lead.priority"
                                         v-text="$t(`common.${lead.priority}`)"></div>
                                </div>
                                <div class="lead-block">
                                    <div class="block-label label-gray"  v-text="$t('common.url') +':'">:</div>
                                    <a :href="lead.url" class="block-value" v-text="lead.url"></a>
                                </div>
                                <div class="lead-block">
                                    <div class="block-label label-gray date">
                                        <i class="icon-calendar"></i>
                                    </div>
                                    <div class="block-value" v-text="lead.created_at"></div>
                                </div>

                            </div>
                        </div >
                    </div>
                </div>
            </div>
            <div v-else>{{trans_choice('common.no_even_leadtypes', 1)}}</div>

        </leads-index>

    </div>
@endsection
