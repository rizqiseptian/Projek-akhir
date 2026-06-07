<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 relative z-10">
    @push('styles')
        <style>
            /* Animated background orbs */
            .bg-orbs {
                position: fixed;
                inset: 0;
                z-index: 0;
                pointer-events: none;
            }
            .orb {
                position: absolute;
                border-radius: 50%;
                filter: blur(100px);
                opacity: 0.12;
                animation: float 10s ease-in-out infinite;
            }
            .orb-1 { width: 500px; height: 500px; background: #7c3aed; top: -100px; left: -100px; animation-delay: 0s; }
            .orb-2 { width: 450px; height: 450px; background: #4f46e5; bottom: -100px; right: -100px; animation-delay: 3.5s; }
            .orb-3 { width: 350px; height: 350px; background: #06b6d4; top: 40%; left: 55%; transform: translate(-50%, -50%); animation-delay: 7s; }

            @keyframes float {
                0%, 100% { transform: translateY(0) scale(1); }
                50% { transform: translateY(-25px) scale(1.04); }
            }
        </style>
    @endpush

    <!-- Orbs behind the content -->
    <div class="bg-orbs">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <!-- Header Section -->
    <div class="mb-8 relative z-10">
        <h1 class="text-3xl font-extrabold text-white tracking-tight">Checkout Terminal</h1>
        <p class="mt-2 text-sm text-gray-400">Scan items or input manually to calculate totals and process transactions.</p>
    </div>

    <!-- Success Message Alert -->
    @if ($successMessage)
        <div class="mb-8 bg-emerald-500/10 border border-emerald-500/30 p-4 rounded-xl shadow-[0_4px_20px_rgba(16,185,129,0.1)] relative z-10">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 text-emerald-400">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-emerald-300">{{ $successMessage }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Error Message Alert -->
    @if ($errorMessage)
        <div class="mb-8 bg-rose-500/10 border border-rose-500/30 p-4 rounded-xl shadow-[0_4px_20px_rgba(244,63,94,0.1)] relative z-10">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 text-rose-400">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-rose-300">{{ $errorMessage }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 relative z-10">
        
        <!-- Left Column: Cart Items -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-[#0f0f1b]/70 border border-white/5 backdrop-blur-md rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.5)] overflow-hidden">
                <div class="px-6 py-5 border-b border-white/5 bg-white/[0.02] flex justify-between items-center">
                    <h3 class="text-lg leading-6 font-bold text-white">Shopping Cart</h3>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-violet-500/15 border border-violet-500/30 text-violet-300">
                        {{ count($cart) }} items
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-white/5">
                        <thead class="bg-white/[0.01]">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Item Details</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Price</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-400 uppercase tracking-wider w-16">Remove</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse ($cart as $index => $item)
                                <tr class="hover:bg-white/[0.02] transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-white font-medium">{{ $item['description'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-cyan-400 text-right font-mono font-bold">${{ number_format($item['price'], 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button type="button" wire:click="removeItem({{ $index }})" class="text-rose-400 hover:text-rose-300 focus:outline-none transition-colors cursor-pointer">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-16 text-center text-sm text-gray-400 italic bg-white/[0.01]">
                                        Cart is empty. Use the form on the right to add items.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if(count($cart) > 0)
                            <tfoot class="bg-white/[0.02] border-t border-white/5">
                                <tr>
                                    <td class="px-6 py-5 whitespace-nowrap text-right text-sm font-bold text-gray-300 uppercase">Total</td>
                                    <td class="px-6 py-5 whitespace-nowrap text-right text-xl font-extrabold text-violet-400 font-mono">${{ number_format($this->total, 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column: Add Item & Checkout -->
        <div class="space-y-6">
            
            <!-- Add Item Form -->
            <div class="bg-[#0f0f1b]/70 border border-white/5 backdrop-blur-md rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.5)] overflow-hidden">
                <div class="px-6 py-5 border-b border-white/5 bg-white/[0.02]">
                    <h3 class="text-lg leading-6 font-bold text-white">Add Item</h3>
                </div>
                <div class="px-6 py-6">
                    <form wire:submit.prevent="addItem" class="space-y-5">
                        <div>
                            <label for="description" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Item Name</label>
                            <input type="text" wire:model="newItemDescription" id="description" class="bg-white/5 border border-white/10 text-white rounded-xl focus:border-violet-500/50 focus:ring-1 focus:ring-violet-500/50 placeholder-white/20 py-2.5 px-3 block w-full text-sm outline-none transition-all" placeholder="e.g. Cafe Latte" required>
                        </div>

                        <div>
                            <label for="price" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Price ($)</label>
                            <div class="relative rounded-xl">
                                <div class="absolute inset-y-0 left-0 p
                                l-3.5 flex items-center pointer-events-none">
                                    <span class="text-gray-400 text-sm">$</span>
                                </div>
                                <input type="number" step="0.01" wire:model="newItemPrice" id="price" class="bg-white/5 border border-white/10 text-white rounded-xl focus:border-violet-500/50 focus:ring-1 focus:ring-violet-500/50 placeholder-white/20 py-2.5 pl-8 pr-3 block w-full text-sm outline-none transition-all" placeholder="0.00" required>
                            </div>
                        </div>

                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-lg text-sm font-bold text-white bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 focus:outline-none transition-all duration-200 transform hover:-translate-y-0.5 active:translate-y-0 cursor-pointer shadow-violet-500/10">
                            Add to Order
                        </button>
                    </form>
                </div>
            </div>

            <!-- Checkout / Payment -->
            <div class="bg-[#0f0f1b]/70 border border-white/5 backdrop-blur-md rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.5)] overflow-hidden">
                <div class="px-6 py-5 border-b border-white/5 bg-white/[0.02]">
                    <h3 class="text-lg leading-6 font-bold text-white">Payment</h3>
                </div>
                <div class="px-6 py-6 space-y-6">
                    <div>
                        <label for="cash" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Cash Received ($)</label>
                        <div class="relative rounded-xl">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <span class="text-gray-400 text-sm font-bold">$</span>
                            </div>
                                <input type="number" step="0.01" wire:model="cashPaid" id="cash" class="bg-white/5 border border-white/10 text-white rounded-xl focus:border-violet-500/50 focus:ring-1 focus:ring-violet-500/50 placeholder-white/20 py-3 pl-8 pr-3 block w-full text-xl font-bold outline-none transition-all" placeholder="0.00">
                            <span class="text-xl font-bold font-mono text-violet-400">${{ number_format($this->total, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Change Due</span>
                            <span class="text-2xl font-black font-mono @if($this->change > 0) text-emerald-400 @else text-gray-500 @endif">
                                ${{ number_format($this->change, 2) }}
                            </span>
                        </div>
                    </div>

                    <button 
                        wire:click="completeTransaction"
                        @if(empty($cart) || (float)$cashPaid < $this->total) disabled @endif
                        class="w-full flex justify-center py-4 px-4 border border-transparent rounded-xl shadow-lg text-lg font-extrabold text-white bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-400 hover:to-green-500 focus:outline-none disabled:from-gray-800 disabled:to-gray-800 disabled:text-gray-500 disabled:border-white/5 disabled:cursor-not-allowed disabled:shadow-none transition-all duration-200 transform hover:-translate-y-0.5 active:translate-y-0 cursor-pointer shadow-emerald-500/10"
                    >
                        Complete Transaction
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- Transaction History Section -->
    <div class="mt-12 relative z-10">
        <div class="bg-[#0f0f1b]/70 border border-white/5 backdrop-blur-md rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.5)] overflow-hidden">
            <div class="px-6 py-5 border-b border-white/5 bg-white/[0.02]">
                <h3 class="text-lg leading-6 font-bold text-white">Recent Transactions</h3>
            </div>
            
            @if($transactions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-white/5">
                        <thead class="bg-white/[0.01]">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Items</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Total</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Cash</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Change</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach ($transactions as $transaction)
                                <tr class="hover:bg-white/[0.02] transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ $transaction->created_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-300">
                                        <div class="space-y-1">
                                            @foreach ($transaction->items as $item)
                                                <div class="text-xs text-gray-400">{{ $item->description }} (${{ number_format($item->price, 2) }})</div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-violet-400 text-right font-mono font-bold">
                                        ${{ number_format($transaction->total_amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-cyan-400 text-right font-mono">
                                        ${{ number_format($transaction->cash_paid, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-emerald-400 text-right font-mono font-bold">
                                        ${{ number_format($transaction->change_returned, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-6 py-16 text-center text-sm text-gray-400 italic bg-white/[0.01]">
                    No transactions yet. Complete a transaction to see it here.
                </div>
            @endif
        </div>
    </div>
</div>

