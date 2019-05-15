{{-- component for change custom fields depends on lead type --}}
<lead-modal :data-leadtypes="{{ json_encode($leadtypes) }}"
            :data-selected="{{ json_encode($lead->lead_type_id) }}"
            inline-template>
    <div class="container-fluid">
        <form action="{{ route('leads.update', $lead) }}"
              method="POST">

            {{ csrf_field() }}
            {{ method_field('PATCH') }}

            <div class="form-group">
                <label for="name">{{ trans_choice('common.name', 1) }}</label>
                <input type="text"
                       class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                       name="name"
                       id="name"
                       value="{{ old('name') ?? $lead->name }}">

                @if ($errors->has('name'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label for="url">{{ trans_choice('common.url', 1) }}</label>
                <input type="url"
                       class="form-control{{ $errors->has('url') ? ' is-invalid' : '' }}"
                       id="url"
                       name="url"
                       value="{{ old('url') ?? $lead->url }}">

                @if ($errors->has('url'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('url') }}</strong>
                    </span>
                @endif
            </div>

            <div class="row">
                <div class="form-group col-6">
                    <label class=""
                           for="lead_type_id">@lang('common.type')</label>
                    <select class="form-control{{ $errors->has('lead_type_id') ? ' is-invalid' : '' }}"
                            name="lead_type_id"
                            id="lead_type_id"
                            v-model="selected_id">
                        <option v-for="leadtype in leadtypes"
                                :value="leadtype.id"
                                v-text="leadtype.name"></option>
                    </select>

                    @if ($errors->has('lead_type_id'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('lead_type_id') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group col-6">
                    <label for="client_id">{{ trans_choice('models.clients', 1) }}</label>
                    <select class="form-control{{ $errors->has('client_id') ? ' is-invalid' : '' }}"
                            name="client_id" id="client_id">
                        <option value="0">No Client</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}"
                                    {{ old('client_id') == $client->id ? 'selected' :
                                    $client->id == $lead->client_id ? 'selected' : '' }}>
                                {{ $client->name }}
                            </option>
                        @endforeach
                    </select>

                    @if ($errors->has('client_id'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('client_id') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            @if (auth()->user()->isHighLevelAccess())
                <div class="form-group">
                    <label for="manager_id">{{ trans_choice('models.managers', 1)}}</label>
                    <select id="manager_id"
                            class="form-control{{ $errors->has('manager_id') ? ' is-invalid' : '' }}"
                            name="manager_id">
                        @foreach($managers as $manager)
                            @if($manager->active == 1)
                                <option value="{{ $manager->id }}"
                                        {{ old('manager_id') == $manager->id ? 'selected' :
                                        $manager->id == $lead->manager_id ? 'selected' : '' }}>
                                    {{ $manager->name }}
                                </option>
                            @endif
                        @endforeach
                    </select>

                    @if ($errors->has('manager_id'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('manager_id') }}</strong>
                        </span>
                    @endif
                </div>
            @endif

            <div class="form-group">
                <label>@lang("common.priority")</label>
                <div class="radio-group">
                    @foreach(config('constants.PRIORITIES') as $priority)
                        <input class="radio form-control{{ $errors->has('priority') ? ' is-invalid' : '' }}"
                               name="priority"
                               id="{{$priority}}"
                               type="radio"
                               value="{{$priority}}"
                                {{ old('priority') == $priority ? 'checked' :
                                $priority == $lead->priority ? 'checked' : '' }}>
                        <label class="radio-label" for="{{$priority}}">
                            <div class="radio-check">
                                <div class="inner-check"></div>
                            </div>
                            <span>{{ucfirst( trans_choice('common.'.$priority, 1) )}}</span>
                        </label>
                    @endforeach

                    @if ($errors->has('priority'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('priority') }}</strong>
                        </span>
                    @endif
                </div>
            </div>


            <div class="form-group">
                <label>@lang("common.payment_type")</label>
                <div class="radio-group">
                    @foreach(config('constants.PAYMENT_TYPES') as $payment_type)
                        <input class="radio form-control{{ $errors->has('payment_type') ? ' is-invalid' : '' }}"
                               name="payment_type"
                               id="{{$payment_type}}"
                               type="radio"
                               value="{{$payment_type}}"
                                {{ old('payment_type') == $payment_type ? 'checked' :
                                $payment_type == $lead->payment_type ? 'checked' : '' }}>
                        <label class="radio-label"
                               for="{{$payment_type}}">
                            <div class="radio-check">
                                <div class="inner-check"></div>
                            </div>
                            <span>{{ucfirst(trans_choice('common.'.$payment_type, 1))}}</span>
                        </label>
                    @endforeach
                    @if ($errors->has('payment_type'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('payment_type') }}</strong>
                        </span>
                    @endif
                </div>


                <input class="form-control{{ $errors->has('payment_quantity') ? ' is-invalid' : '' }}"
                       title="payment_quantity" min="0"
                       type="number" name="payment_quantity"
                       value="{{ old('payment_quantity') ?? $lead->payment_quantity }}"
                >
                @if ($errors->has('payment_quantity'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('payment_quantity') }}</strong>
                    </span>
                @endif
            </div>


            <div class="form-group">
                <label for="comment">{{ trans_choice('common.comment', 1) }}</label>
                <textarea id="comment"
                          class="form-control{{ $errors->has('comment') ? ' is-invalid' : '' }}"
                          name="comment">{{ old('comment') ?? $lead->comment }}</textarea>
                @if ($errors->has('comment'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('comment') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group d-flex justify-content-center">
                <button type="submit" class="button button-green mr-2">
                    {{trans_choice('common.save', 1)}}
                </button>
                <button type="button" class="button button-red" data-dismiss="modal">
                    {{trans_choice('common.cancel', 1)}}
                </button>
            </div>
        </form>
    </div>
</lead-modal>