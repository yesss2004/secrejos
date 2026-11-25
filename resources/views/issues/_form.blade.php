@php
    $editing = $issue !== null;
@endphp

<form method="POST" action="{{ $action }}" class="max-w-3xl">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="card space-y-5 p-5 sm:p-6">
        <div>
            <label for="title" class="label">Hambatan <span class="text-rose-500">*</span></label>
            <input id="title" name="title" type="text" value="{{ old('title', $editing ? $issue->title : '') }}" required maxlength="160"
                   placeholder="Ringkas kendala yang terjadi"
                   class="input @error('title') !border-rose-400 @enderror">
            @error('title')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
        </div>

        <div>
            <label for="cause" class="label">Penyebab <span class="text-rose-500">*</span></label>
            <textarea id="cause" name="cause" rows="4" required maxlength="2000"
                      placeholder="Jelaskan penyebab kendala"
                      class="textarea @error('cause') !border-rose-400 @enderror">{{ old('cause', $editing ? $issue->cause : '') }}</textarea>
            @error('cause')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
        </div>

        <div>
            <label for="follow_up" class="label">Tindak lanjut</label>
            <textarea id="follow_up" name="follow_up" rows="4" maxlength="2000"
                      placeholder="Langkah yang akan atau sudah dilakukan"
                      class="textarea @error('follow_up') !border-rose-400 @enderror">{{ old('follow_up', $editing ? $issue->follow_up : '') }}</textarea>
            @error('follow_up')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="priority" class="label">Prioritas <span class="text-rose-500">*</span></label>
                <select id="priority" name="priority" required class="select @error('priority') !border-rose-400 @enderror">
                    @foreach($priorities as $value => $label)
                        <option value="{{ $value }}" @selected(old('priority', $editing ? $issue->priority : 'sedang') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('priority')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
            </div>

            @if($editing)
                <div>
                    <label for="status" class="label">Status <span class="text-rose-500">*</span></label>
                    <select id="status" name="status" required class="select @error('status') !border-rose-400 @enderror">
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $issue->status) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
                </div>
            @endif
        </div>

        <div class="flex flex-col-reverse gap-2 border-t border-slate-100 pt-4 sm:flex-row sm:justify-between">
            <a href="{{ route('issues.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary"><x-icon name="save" class="h-4 w-4" /> {{ $submitLabel }}</button>
        </div>
    </div>
</form>
