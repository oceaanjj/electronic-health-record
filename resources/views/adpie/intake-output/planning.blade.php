@extends('layouts.app')

@section('title', 'Patient Medical History')

@section('content')

<center>
  <table class="mb-1.5 w-[72%] border-separate border-spacing-0">
                <tr>
                    <th rowspan="2" class="w-[200px] bg-dark-green text-white rounded-l-lg">PLANNING</th>
                    <th
                        class="bg-yellow-light text-brown text-[13px] border-l-2 border-r-2 border-t-2 border-line-brown rounded-tr-lg">
                        FINDINGS
                    </th>

                    <th
                        class="bg-yellow-light text-brown text-[13px] border-l-2 border-r-2 border-t-2 border-line-brown rounded-tr-lg">
                        DECISION SUPPORT
                    </th>

                </tr>

                <tr>
                    <td class="rounded-br-lg">
                        <textarea class="notepad-lines h-[100px]" name="gross_motor"
                            placeholder="Type here..."></textarea>
                    </td>


                     {{-- dito yung suggestions hindi ko muna lalagyan css put muna kayo basta nag shoshow text --}}
                    <td class="w-[200px]"></td>
                </tr>
            </table>
</center>

@endsection