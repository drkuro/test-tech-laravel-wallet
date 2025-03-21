<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('Dashboard') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5">
        <h2 class="text-xl font-bold mb-6">@lang('Recurring transfers list')</h2>
        @if($reccurings)
        <table class="w-full text-sm text-left text-gray-500 border border-gray-200">
          <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3">
                @lang('ID')
              </th>
              <th scope="col" class="px-6 py-3">
                @lang('Start at')
              </th>
              <th scope="col" class="px-6 py-3">
                @lang('End At')
              </th>
              <th scope="col" class="px-6 py-3">
                @lang('Reason')
              </th>
              <th scope="col" class="px-6 py-3">
                @lang('Email')
              </th>
              <th scope="col" class="px-6 py-3">
                @lang('Amount')
              </th>
              <th scope="col" class="px-6 py-3">
                @lang('action')
              </th>
            </tr>
          </thead>
          <tbody>
            @foreach($reccurings as $reccuring)
            <tr class="bg-white border-b">
              <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                {{$reccuring->id}}
              </th>
              <td class="px-6 py-4">
                {{$reccuring->start_at}}
              </td>
              <td class="px-6 py-4">
                {{$reccuring->end_at}}
              </td>
              <td class="px-6 py-4">
                {{$reccuring->reason}}
              </td>
              <td class="px-6 py-4">
                {{$reccuring->recipient_email}}
              </td>
              <td class="px-6 py-4">
                {{Number::currencyCents($reccuring->amount)}}
              </td>
              <td class="px-6 py-4">
                <form action="{{ route('recurring.delete', [$reccuring]) }}" method="POST">
                  <input type="hidden" name="_method" value="DELETE">
                  <input type="hidden" name="_token" value="{{ csrf_token() }}">
                  <button type="submit">
                    {{ __('Delete') }}
                  </button>
                </form>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
        @else
        @endif
      </div>
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5">
        <h2 class="text-xl font-bold mb-6">@lang('Create a new recurring transfert')</h2>
        <form method="POST" action="{{ route('recurring.store') }}" class="space-y-4">
          @csrf

          @if (session('money-sent-status') === 'success')
          <div class="p-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
            <span class="font-medium">@lang('Money sent!')</span>
            @lang(':amount were successfully sent to :name.', ['amount' =>
            Number::currencyCents(session('money-sent-amount', 0)), 'name' =>
            session('money-sent-recipient-name')])
          </div>
          @elseif (session('money-sent-status') === 'insufficient-balance')
          <div class="p-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
            <span class="font-medium">@lang('Insufficient balance!')</span>
            @lang('You can\'t send :amount to :name.', ['amount' =>
            Number::currencyCents(session('money-sent-amount', 0)), 'name' =>
            session('money-sent-recipient-name')])
          </div>
          @endif

          <div>
            <x-input-label for="start_at" :value="__('Start at')" />
            <x-text-input id="start_at" class="block mt-1 w-full" type="date" name="start_at" :value="old('start_at')"
              required />
            <x-input-error :messages="$errors->get('start_at')" class="mt-2" />
          </div>
          <div>
            <x-input-label for="end_at" :value="__('End at')" />
            <x-text-input id="end_at" class="block mt-1 w-full" type="date" name="end_at" :value="old('end_at')"
              required />
            <x-input-error :messages="$errors->get('end_at')" class="mt-2" />
          </div>
          <div>
            <x-input-label for="frequency" :value="__('frequency (days)')" />
            <x-text-input id="frequency" class="block mt-1 w-full" type="number" min="1" step="1" :value="old('amount')"
              name="frequency" required />
            <x-input-error :messages="$errors->get('frequency')" class="mt-2" />
          </div>
          <div>
            <x-input-label for="recipient_email" :value="__('Recipient email')" />
            <x-text-input id="recipient_email" class="block mt-1 w-full" type="email" name="recipient_email"
              :value="old('recipient_email')" required />
            <x-input-error :messages="$errors->get('recipient_email')" class="mt-2" />
          </div>
          <div>
            <x-input-label for="amount" :value="__('Amount (€)')" />
            <x-text-input id="amount" class="block mt-1 w-full" type="number" min="0" step="0.01" :value="old('amount')"
              name="amount" required />
            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
          </div>
          <div>
            <x-input-label for="reason" :value="__('Reason')" />
            <x-text-input id="reason" class="block mt-1 w-full" type="text" :value="old('reason')" name="reason"
              required />
            <x-input-error :messages="$errors->get('reason')" class="mt-2" />
          </div>

          <div class="flex justify-end mt-4">
            <x-primary-button>
              {{ __('Validate !') }}
            </x-primary-button>
          </div>
        </form>
      </div>
    </div>
  </div>
</x-app-layout>