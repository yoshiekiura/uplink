<table>
    <thead>
        <tr>
            <th colspan="2">{{ $user->name }}'s Sales Report for {{ $period }}</th>
        </tr>
        <tr>
            <th>Date</th>
            <th>Grand Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($datas as $item)
            <tr>
                <td>{{ $item->created_at }}</td>
                <td>{{ $item->grand_total }}</td>
            </tr>
        @endforeach
    </tbody>
</table>