<form action="{{ route('lead.reassign', $lead) }}" method="POST">
    {{csrf_field()}}
    <div class="form-group">
        <label for="manager_id">{{ trans_choice('models.managers', 1)}}</label>
        <select id="manager_id"
                class="form-control"
                name="manager_id">
            @foreach($managers as $manager)
                <option value="{{ $manager->id }}"
                        {{ old('manager_id') == $manager->id ? 'selected' :
                        $manager->id == $lead->manager_id ? 'selected' : '' }}>
                    {{ $manager->name }}
                </option>
            @endforeach
        </select>

        @if ($errors->has('manager_id'))
            <span class="invalid-feedback">
                <strong>{{ $errors->first('manager_id') }}</strong>
            </span>
        @endif
    </div>
    <div class="form-group d-flex justify-content-center">
        <button type="submit" class="button button-green mr-2">
            Save
        </button>
        <button type="button" class="button button-red" data-dismiss="modal">
            Cancel
        </button>
    </div>
</form>