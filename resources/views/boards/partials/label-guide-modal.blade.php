<!-- Label Guide Modal -->
<div id="labelGuideModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-6 border-0 w-full max-w-4xl shadow-xl rounded-xl bg-white">
        <div class="flex justify-between items-center mb-5 pb-3 border-b">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-green-500 to-blue-500 flex items-center justify-center text-white text-xl">
                    📖
                </div>
                <div>
                    <h3 class="text-xl font-bold" style="color: #1e3a5f;">Panduan Penggunaan Label</h3>
                    <p class="text-sm text-gray-500">Rekomendasi label untuk memaksimalkan produktivitas tim</p>
                </div>
            </div>
            <button onclick="closeLabelGuideModal()" class="text-gray-400 hover:text-gray-600 text-2xl transition">&times;</button>
        </div>
        
        <div class="max-h-[70vh] overflow-y-auto pr-2">
            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-6">
                <div class="bg-blue-50 rounded-xl p-3 text-center">
                    <div class="text-2xl">🏷️</div>
                    <div class="font-bold text-blue-700">10+</div>
                    <div class="text-xs text-gray-500">Label Rekomendasi</div>
                </div>
                <div class="bg-green-50 rounded-xl p-3 text-center">
                    <div class="text-2xl">📋</div>
                    <div class="font-bold text-green-700">8 Kategori</div>
                    <div class="text-xs text-gray-500">Jenis Label</div>
                </div>
                <div class="bg-purple-50 rounded-xl p-3 text-center">
                    <div class="text-2xl">🎯</div>
                    <div class="font-bold text-purple-700">Best Practice</div>
                    <div class="text-xs text-gray-500">Tips Penggunaan</div>
                </div>
                <div class="bg-orange-50 rounded-xl p-3 text-center">
                    <div class="text-2xl">💡</div>
                    <div class="font-bold text-orange-700">Contoh</div>
                    <div class="text-xs text-gray-500">Penerapan Nyata</div>
                </div>
            </div>
            
            <!-- Label Categories -->
            <div class="mb-6">
                <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <span class="w-1 h-5 bg-red-500 rounded-full"></span>
                    🔴 Priority & Urgency Labels
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 ml-2">
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <span class="label-badge" style="background-color: #ef4444; color: white; padding: 4px 12px;">Urgent</span>
                        <div>
                            <p class="font-medium text-gray-800">Task yang harus segera dikerjakan</p>
                            <p class="text-xs text-gray-500">Deadline mendadak, blocking issue, critical bug</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <span class="label-badge" style="background-color: #f97316; color: white; padding: 4px 12px;">High Priority</span>
                        <div>
                            <p class="font-medium text-gray-800">Prioritas tinggi, kerjakan setelah urgent</p>
                            <p class="text-xs text-gray-500">Fitur penting untuk release</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <span class="label-badge" style="background-color: #3b82f6; color: white; padding: 4px 12px;">Medium Priority</span>
                        <div>
                            <p class="font-medium text-gray-800">Prioritas normal</p>
                            <p class="text-xs text-gray-500">Task reguler yang tidak urgent</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <span class="label-badge" style="background-color: #6b7280; color: white; padding: 4px 12px;">Low Priority</span>
                        <div>
                            <p class="font-medium text-gray-800">Prioritas rendah</p>
                            <p class="text-xs text-gray-500">Nice to have, bisa dikerjakan nanti</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mb-6">
                <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <span class="w-1 h-5 bg-green-500 rounded-full"></span>
                    🟢 Task Type Labels
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 ml-2">
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <span class="label-badge" style="background-color: #22c55e; color: white; padding: 4px 12px;">Feature</span>
                        <div>
                            <p class="font-medium text-gray-800">Fitur baru yang akan dikembangkan</p>
                            <p class="text-xs text-gray-500">Pengembangan dari nol</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <span class="label-badge" style="background-color: #3b82f6; color: white; padding: 4px 12px;">Enhancement</span>
                        <div>
                            <p class="font-medium text-gray-800">Peningkatan fitur yang sudah ada</p>
                            <p class="text-xs text-gray-500">Improvement, optimization</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <span class="label-badge" style="background-color: #ef4444; color: white; padding: 4px 12px;">Bug</span>
                        <div>
                            <p class="font-medium text-gray-800">Error atau masalah pada sistem</p>
                            <p class="text-xs text-gray-500">Perbaikan bug, issue, error handling</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <span class="label-badge" style="background-color: #6b7280; color: white; padding: 4px 12px;">Documentation</span>
                        <div>
                            <p class="font-medium text-gray-800">Dokumentasi teknis atau user guide</p>
                            <p class="text-xs text-gray-500">README, API docs, wiki</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mb-6">
                <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <span class="w-1 h-5 bg-yellow-500 rounded-full"></span>
                    🟡 Status & Progress Labels
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 ml-2">
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <span class="label-badge" style="background-color: #6b7280; color: white; padding: 4px 12px;">Backlog</span>
                        <div>
                            <p class="font-medium text-gray-800">Ide/perencanaan, belum dikerjakan</p>
                            <p class="text-xs text-gray-500">Tertunda, menunggu resource</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <span class="label-badge" style="background-color: #3b82f6; color: white; padding: 4px 12px;">To Do</span>
                        <div>
                            <p class="font-medium text-gray-800">Akan dikerjakan selanjutnya</p>
                            <p class="text-xs text-gray-500">Ready to start</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <span class="label-badge" style="background-color: #eab308; color: white; padding: 4px 12px;">In Progress</span>
                        <div>
                            <p class="font-medium text-gray-800">Sedang dalam pengerjaan</p>
                            <p class="text-xs text-gray-500">Actively working</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <span class="label-badge" style="background-color: #a855f7; color: white; padding: 4px 12px;">In Review</span>
                        <div>
                            <p class="font-medium text-gray-800">Menunggu review/approval</p>
                            <p class="text-xs text-gray-500">Code review, QA, approval</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <span class="label-badge" style="background-color: #f97316; color: white; padding: 4px 12px;">Testing</span>
                        <div>
                            <p class="font-medium text-gray-800">Dalam tahap testing/QA</p>
                            <p class="text-xs text-gray-500">UAT, regression test</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <span class="label-badge" style="background-color: #22c55e; color: white; padding: 4px 12px;">Done</span>
                        <div>
                            <p class="font-medium text-gray-800">Sudah selesai</p>
                            <p class="text-xs text-gray-500">Completed, finished</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mb-6">
                <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <span class="w-1 h-5 bg-orange-500 rounded-full"></span>
                    🚧 Blockers & Issues
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 ml-2">
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <span class="label-badge" style="background-color: #ef4444; color: white; padding: 4px 12px;">Blocked</span>
                        <div>
                            <p class="font-medium text-gray-800">Task terblokir karena dependensi</p>
                            <p class="text-xs text-gray-500">Menunggu pihak lain, ada issue teknis</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <span class="label-badge" style="background-color: #eab308; color: white; padding: 4px 12px;">Question</span>
                        <div>
                            <p class="font-medium text-gray-800">Membutuhkan klarifikasi</p>
                            <p class="text-xs text-gray-500">Waiting for answer, need discussion</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mb-6">
                <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <span class="w-1 h-5 bg-purple-500 rounded-full"></span>
                    🎨 Team & Department Labels
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3 ml-2">
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <span class="label-badge" style="background-color: #3b82f6; color: white; padding: 4px 12px;">Frontend</span>
                        <div><p class="font-medium text-gray-800">Frontend Development</p></div>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <span class="label-badge" style="background-color: #a855f7; color: white; padding: 4px 12px;">Backend</span>
                        <div><p class="font-medium text-gray-800">Backend Development</p></div>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <span class="label-badge" style="background-color: #f97316; color: white; padding: 4px 12px;">Design</span>
                        <div><p class="font-medium text-gray-800">UI/UX Design</p></div>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <span class="label-badge" style="background-color: #22c55e; color: white; padding: 4px 12px;">QA</span>
                        <div><p class="font-medium text-gray-800">Quality Assurance</p></div>
                    </div>
                </div>
            </div>
            
            <!-- Best Practices Tips -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 mb-6">
                <h4 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                    <span>💡</span> Best Practice Tips
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="flex items-start gap-2">
                        <span class="text-green-500">✓</span>
                        <p class="text-sm text-gray-700">Gunakan <strong>kombinasi label</strong> untuk informasi lebih lengkap (contoh: Bug + Urgent)</p>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="text-green-500">✓</span>
                        <p class="text-sm text-gray-700"><strong>Konsisten</strong> dengan warna di semua project</p>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="text-green-500">✓</span>
                        <p class="text-sm text-gray-700">Maksimal <strong>5-10 label</strong> per board agar tidak bingung</p>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="text-green-500">✓</span>
                        <p class="text-sm text-gray-700">Gunakan nama label yang <strong>jelas dan singkat</strong></p>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="text-green-500">✓</span>
                        <p class="text-sm text-gray-700">Label untuk <strong>kategori</strong>, bukan untuk nama spesifik task</p>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="text-green-500">✓</span>
                        <p class="text-sm text-gray-700"><strong>Evaluasi</strong> label secara berkala, hapus yang tidak terpakai</p>
                    </div>
                </div>
            </div>
            
            <!-- Example Task -->
            <div class="bg-gray-100 rounded-xl p-4">
                <h4 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                    <span>📝</span> Contoh Task dengan Label
                </h4>
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <div class="flex flex-wrap gap-2 mb-2">
                        <span class="label-badge" style="background-color: #ef4444; color: white; padding: 4px 12px;">Bug</span>
                        <span class="label-badge" style="background-color: #ef4444; color: white; padding: 4px 12px;">Urgent</span>
                        <span class="label-badge" style="background-color: #3b82f6; color: white; padding: 4px 12px;">Frontend</span>
                    </div>
                    <p class="font-medium text-gray-800">Fix login page error on Safari browser</p>
                    <p class="text-sm text-gray-500 mt-1">Interpretasi: Bug urgent di frontend yang harus segera diperbaiki</p>
                </div>
                <div class="bg-white rounded-lg p-4 shadow-sm mt-3">
                    <div class="flex flex-wrap gap-2 mb-2">
                        <span class="label-badge" style="background-color: #22c55e; color: white; padding: 4px 12px;">Feature</span>
                        <span class="label-badge" style="background-color: #a855f7; color: white; padding: 4px 12px;">Design</span>
                        <span class="label-badge" style="background-color: #eab308; color: white; padding: 4px 12px;">In Progress</span>
                    </div>
                    <p class="font-medium text-gray-800">Create dark mode toggle button UI component</p>
                    <p class="text-sm text-gray-500 mt-1">Interpretasi: Fitur baru dengan desain, sedang dalam pengerjaan</p>
                </div>
            </div>
        </div>
        
        <div class="mt-6 flex justify-end gap-3 border-t pt-4">
            <button onclick="closeLabelGuideModal()" class="px-5 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition font-medium">Close</button>
            <button onclick="openLabelsModal(); closeLabelGuideModal();" class="px-5 py-2 btn-accent rounded-lg font-medium">
                🏷️ Manage Labels
            </button>
        </div>
    </div>
</div>

<script>
function openLabelGuideModal() {
    document.getElementById('labelGuideModal').classList.remove('hidden');
}

function closeLabelGuideModal() {
    document.getElementById('labelGuideModal').classList.add('hidden');
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('labelGuideModal');
    if (event.target === modal) {
        closeLabelGuideModal();
    }
});
</script>