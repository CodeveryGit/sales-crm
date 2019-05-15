<div class="modal fade"
     id="lead-modal"
     tabindex="-1"
     role="dialog"
     aria-labelledby="#lead-modal"
     aria-hidden="true"
     data-show="{{ ($errors->any()) ? 'true' : 'false' }}">

    <div class="modal-dialog modal-dialog-centered" role="document">
        @if(!empty($leadtypes->toArray()))
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">
                        {{($action == 'create') ? __('common.create') :  __('common.edit')}}
                        <span class="text-lowercase">{{ trans_choice('models.leads', 1)}}</span>
                    </h2>
                </div>
                <div class="modal-body">

                    @if($action == 'create')
                        @include('leads.forms._create')
                    @else
                        @can('update', $lead)
                            @include('leads.forms._edit')
                        @elsecan('reassign', $lead)
                            @include('leads.forms._reassign')
                        @endcan
                    @endif

                </div>
            </div>
        @else
            <div class="modal-content">@lang('common.no_even_leadtypes')</div>
        @endif
    </div>
</div>
