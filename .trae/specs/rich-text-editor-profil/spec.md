# Rich Text Editor untuk Profil Desa Spec

## Why

Halaman admin profil desa menggunakan `<textarea>` polos untuk input Visi, Misi, dan Sejarah. Data ini seharusnya bisa diformat (bold, italic, list, link) untuk tampilan publik yang lebih baik.

## What Changes

- Buat komponen `TiptapEditor.vue` reusable dengan toolbar minimalis
- Ganti textarea Visi, Misi, Sejarah di Edit.vue dengan TiptapEditor
- Desain toolbar editor sesuai estetika aplikasi (warm earth tones, elegant, minimal)
- Kompatibel dengan `v-model`/`useForm` Inertia

## Impact

- Affected specs: Admin Profile Page
- Affected code:
  - `resources/js/components/TiptapEditor.vue` (new)
  - `resources/js/pages/Admin/Profil/Edit.vue` (modified)
  - `package.json` (tiptap dependencies sudah ada)

## ADDED Requirements

### Requirement: TiptapEditor Component
The system SHALL provide a reusable Vue 3 rich text editor component built on Tiptap.

#### Scenario: Basic editing
- **GIVEN** the TiptapEditor component is rendered
- **WHEN** user types text
- **THEN** text is displayed and editable

#### Scenario: Toolbar actions
- **GIVEN** the TiptapEditor component is rendered
- **WHEN** user selects text and clicks Bold/Italic/List/Link button
- **THEN** formatting is applied to selected text

#### Scenario: v-model binding
- **GIVEN** the TiptapEditor is bound with v-model
- **WHEN** user edits content
- **THEN** the bound model is updated with HTML content

### Requirement: Visi/Misi/Sejarah Editor
The profile edit page SHALL use TiptapEditor for Visi, Misi, and Sejarah fields.

#### Scenario: Replace textareas
- **GIVEN** the Admin/Profil/Edit page
- **WHEN** page renders
- **THEN** Visi, Misi, Sejarah fields display TiptapEditor instead of `<textarea>`

#### Scenario: Form submission
- **GIVEN** the Admin/Profil/Edit page with edited rich content
- **WHEN** user clicks Simpan Perubahan
- **THEN** HTML content is submitted and saved to database

## MODIFIED Requirements

### Requirement: Profile edit form layout
Visi & Misi card and Sejarah card now contain TiptapEditor components with toolbars instead of plain textareas.

## REMOVED Requirements
(none)
