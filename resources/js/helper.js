export function statusClass(status) {
    return {
        'bg-green-100 text-green-800': status === 'completed',
        'bg-red-100 text-red-800': status === 'canceled',
        'bg-purple-100 text-purple-800': status === 'reserved',
        'bg-yellow-100 text-yellow-800': status === 'pending',
    };
}