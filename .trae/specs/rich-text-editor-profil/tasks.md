# Tasks

- [x] Task 1: Buat komponen `TiptapEditor.vue`
  - [x] 1.1 Setup Tiptap dengan StarterKit, Link, Image extensions
  - [x] 1.2 Toolbar minimal: Bold, Italic, Heading, Bullet List, Ordered List, Link, Undo/Redo
  - [x] 1.3 Styling toolbar warm/elegant sesuai desain aplikasi (warna earth tone, border halus)
  - [x] 1.4 Props: `modelValue` (string HTML), placeholder
  - [x] 1.5 Emit `update:modelValue` untuk v-model compatibility
  - [x] 1.6 Style editor area konsisten dengan form input (border, radius, shadow, focus ring)
- [x] Task 2: Ganti textarea Visi, Misi, Sejarah di Edit.vue dengan TiptapEditor
  - [x] 2.1 Import dan registrasi TiptapEditor
  - [x] 2.2 Replace `<textarea id="visi">` dengan `<TiptapEditor v-model="form.visi" />`
  - [x] 2.3 Replace `<textarea id="misi">` dengan `<TiptapEditor v-model="form.misi" />`
  - [x] 2.4 Replace `<textarea id="sejarah">` dengan `<TiptapEditor v-model="form.sejarah" />`
  - [x] 2.5 Atur tinggi minimal editor sesuai konten (visi pendek, misi sedang, sejarah panjang)

# Task Dependencies

- [Task 2] depends on [Task 1]
