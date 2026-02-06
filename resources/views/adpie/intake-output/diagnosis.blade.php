@extends('layouts.app')

@section('title', 'Patient Medical History')

@section('content')
    <center>
        <table class="mb-1.5 w-[72%] border-separate border-spacing-0">
            <tr>
                <th rowspan="2" class="bg-dark-green w-[200px] rounded-l-lg text-white">DIAGNOSIS</th>
                <th
                    class="bg-yellow-light text-brown border-line-brown rounded-tr-lg border-t-2 border-r-2 border-l-2 text-[13px]"
                >
                    FINDINGS
                </th>

                <th
                    class="bg-yellow-light text-brown border-line-brown rounded-tr-lg border-t-2 border-r-2 border-l-2 text-[13px]"
                >
                    DECISION SUPPORT
                </th>
            </tr>

            <tr>
                <td class="rounded-br-lg">
                    <textarea class="notepad-lines h-[100px]" name="gross_motor" placeholder="Type here..."></textarea>
                </td>

                {{-- dito yung suggestions hindi ko muna lalagyan css put muna kayo basta nag shoshow text --}}
                <td class="w-[200px]"></td>
            </tr>
        </table>
    </center>
@endsection
