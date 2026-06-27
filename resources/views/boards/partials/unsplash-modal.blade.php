<!-- Modal Unsplash Picker -->
<div id="unsplashModal" class="hidden fixed inset-0 bg-gray-900/70 backdrop-blur-sm overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-6 border-0 w-full max-w-5xl shadow-2xl rounded-2xl bg-white">
        <div class="flex justify-between items-center mb-5 pb-3 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-600 to-purple-500 flex items-center justify-center shadow-md">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Unsplash Cover</h3>
                    <p class="text-sm text-gray-500">Search millions of free high-resolution photos</p>
                </div>
            </div>
            <button onclick="closeUnsplashModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-full transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <!-- Search Bar -->
        <div class="mb-5">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" id="unsplashSearchInput" placeholder="Search for photos (nature, office, team, landscape, technology...)" 
                       class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm">
                <button onclick="searchUnsplash()" class="absolute right-2 top-1/2 transform -translate-y-1/2 px-4 py-1.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-all duration-200 text-sm cursor-pointer">
                    Search
                </button>
            </div>
            <p class="text-xs text-gray-400 mt-2 flex items-center gap-2">
                <span>💡</span> Powered by Unsplash • Photos provided by talented photographers worldwide
            </p>
        </div>
        
        <!-- Loading State -->
        <div id="unsplashLoading" class="hidden text-center py-12">
            <div class="inline-block w-10 h-10 border-2 border-gray-200 border-t-purple-500 rounded-full animate-spin"></div>
            <p class="text-gray-500 mt-3">Loading photos...</p>
        </div>
        
        <!-- Results Grid -->
        <div id="unsplashResults" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 max-h-[500px] overflow-y-auto p-1 custom-scrollbar">
            <div class="col-span-full text-center text-gray-400 py-12">
                <svg class="w-20 h-20 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <p>Start typing to search for photos</p>
                <p class="text-sm mt-1">Or browse random photos below</p>
                <button onclick="loadRandomPhotos()" class="mt-4 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-all duration-200 cursor-pointer">
                    Load Random Photos
                </button>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="mt-5 pt-3 border-t border-gray-100 flex justify-between items-center">
            <div class="text-xs text-gray-400">
                Photos by <a href="https://unsplash.com" target="_blank" class="text-purple-500 hover:underline">Unsplash</a>
            </div>
            <button onclick="closeUnsplashModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all duration-200 cursor-pointer">Close</button>
        </div>
    </div>
</div>

<style>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
@keyframes spin {
    to { transform: rotate(360deg); }
}
.animate-spin {
    animation: spin 0.6s linear infinite;
}
</style>

<script>
// ==============================================
// UNSPLASH PICKER FUNCTIONS
// ==============================================

let current