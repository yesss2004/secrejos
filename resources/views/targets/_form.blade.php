@php
    $editing = $target !== null;
@endphp

<form method="POST" action="{{ $action }}" class="max-w-3xl">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="card space-y-5 p-5 sm:p-6">
        <div>
            <label for="title" class="label">Nama target / kegiatan <span class="text-rose-500">*</span></label>
            <input id="title" name="title" type="text" value="{{ old('title', $editing ? $target->title : '') }}" required maxlength="160"
                   placeholder="Contoh: Pajak Kendaraan Dinas"
                   class="input @error('title') !border-rose-400 @enderror">
            @error('title')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
        </div>

        <div>
            <label for="description" class="label">Catatan penyelesaian</label>
            <textarea id="description" name="description" rows="4" maxlength="2000"
                      placeholder="Detail target dan hasil yang ingin dicapai"
                      class="textarea @error('description') !border-rose-400 @enderror">{{ old('description', $editing ? $target->description : '') }}</textarea>
            @error('description')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
        </div>

        <div>
            <label for="realization" class="label">Realisasi</label>
            <textarea id="realization" name="realization" rows="3" maxlength="2000"
                      placeholder="Catat hasil atau pekerjaan yang sudah terlaksana"
                      class="textarea @error('realization') !border-rose-400 @enderror">{{ old('realization', $editing ? $target->realization : '') }}</textarea>
            @error('realization')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="due_date" class="label">Tenggat waktu <span class="text-rose-500">*</span></label>
                <input id="due_date" name="due_date" type="date" value="{{ old('due_date', $editing ? $target->due_date->format('Y-m-d') : '') }}" required
                       class="input @error('due_date') !border-rose-400 @enderror">
                @error('due_date')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
            </div>

            <div>
                <label for="priority" class="label">Prioritas <span class="text-rose-500">*</span></label>
                <select id="priority" name="priority" required class="select @error('priority') !border-rose-400 @enderror">
                    @foreach($priorities as $value => $label)
                        <option value="{{ $value }}" @selected(old('priority', $editing ? $target->priority : 'sedang') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('priority')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label for="progress" class="label">Progres (%) <span class="text-rose-500">*</span></label>
            <input id="progress" name="progress" type="number" min="0" max="100" step="1" required
                   value="{{ old('progress', $editing ? $target->progress : 0) }}"
                   class="input max-w-xs @error('progress') !border-rose-400 @enderror">
            @error('progress')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
        </div>

        <div class="flex flex-col-reverse gap-2 border-t border-slate-100 pt-4 sm:flex-row sm:justify-between">
            <a href="{{ route('targets.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary"><x-icon name="save" class="h-4 w-4" /> {{ $submitLabel }}</button>
        </div>
    </div>
</form>
