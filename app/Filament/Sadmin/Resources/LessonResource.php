<?php

namespace App\Filament\Sadmin\Resources;

use AmidEsfahani\FilamentTinyEditor\TinyEditor;
use App\Filament\Sadmin\Resources\LessonResource\Pages;
use App\Filament\Sadmin\Resources\LessonResource\RelationManagers;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuestionOption;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

//use Hugomyb\FilamentMediaAction\Forms\Components\Actions\MediaAction;
//use Illuminate\Database\Eloquent\Builder;
//use Illuminate\Database\Eloquent\SoftDeletingScope;

class LessonResource extends Resource
{
    protected static ?string $model = Lesson::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'Урок';
    protected static ?string $pluralModelLabel = 'Уроки';
    protected static ?string $navigationGroup = 'Академия';
    protected static ?string $navigationLabel = 'Уроки';
    protected static ?string $recordTitleAttribute = 'name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'announcement', 'lesson_content'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Основная информация')
                            ->schema([
                                Forms\Components\Select::make('course_id')
                                    ->label('Курс')
                                    ->relationship('course', 'name')
                                    ->required(),
                                Forms\Components\TextInput::make('name')
                                    ->label('Название урока')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('announcement')
                                    ->label('Анонс')
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('position')
                                    ->label('Позиция урока')
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                                Forms\Components\Toggle::make('is_published')
                                    ->label('Опубликовать')
                                    ->required(),
                            ]),
                        Tabs\Tab::make('Содержание урока')
                            ->schema([
                                TinyEditor::make('lesson_content')
                                    ->fileAttachmentsDisk('public')
                                    ->fileAttachmentsVisibility('public')
                                    ->fileAttachmentsDirectory('lesson_images')
                                    ->profile('custom')
                                    ->columnSpan('full')
                                    ->required(),
/*                                Forms\Components\RichEditor::make('lesson_content')
                                    ->label('Содержание урока')
                                    ->fileAttachmentsDisk('public')
                                    ->fileAttachmentsDirectory('lesson_images')
                                    ->fileAttachmentsVisibility('public')
                                    ->columnSpanFull(),*/
                            ]),
                        Tabs\Tab::make('Медиа')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('Lessons Video')
                                    ->collection('lesson_videos')
                                    ->multiple()
                                    ->panelLayout('grid')
                                    ->reorderable()
                                    ->uploadingMessage('Видео загружается...')
//                                    ->orientImagesFromExif(false)
//                                    ->imagePreviewHeight('250')
//                                    ->loadingIndicatorPosition('left')
//                                    ->panelAspectRatio('2:1')
//                                    ->panelLayout('integrated')
//                                    ->removeUploadedFileButtonPosition('right')
//                                    ->uploadButtonPosition('left')
//                                    ->uploadProgressIndicatorPosition('left'),
/*                                Matinee::make('video')
                                    ->showPreview(),*/
                            ]),
                        Tabs\Tab::make('Тесты')
                            ->schema([
                                Actions::make([
                                    FormAction::make('importQuizJson')
                                        ->label('Импорт теста (JSON)')
                                        ->icon('heroicon-o-arrow-up-tray')
                                        ->form([
                                            Forms\Components\FileUpload::make('quiz_file')
                                                ->label('Файл JSON')
                                                ->acceptedFileTypes(['application/json', '.json', 'text/plain'])
                                                ->maxSize(5 * 1024)
                                                ->disk('local')
                                                ->directory('imports/quizzes')
                                                ->visibility('private')
                                                ->preserveFilenames(),
                                            Forms\Components\Textarea::make('quiz_json')
                                                ->label('JSON (вставьте содержимое, если файл не работает)')
                                                ->rows(16)
                                                ->columnSpanFull(),
                                            Forms\Components\Toggle::make('replace_if_exists')
                                                ->label('Заменить тест с таким же названием')
                                                ->default(false),
                                        ])
                                        ->action(function (array $data, \App\Models\Lesson $record) {
//                                            dd($data);
                                            // Текущий урок доступен как $record из Filament
                                            $lesson = $record;
                                            // Получение данных: либо из загруженного файла, либо из текстового поля
                                            $payload = null;

                                            // Вариант 1: файл
                                            $uploaded = $data['quiz_file'] ?? null;
                                            if ($uploaded) {
                                                $raw = null;

                                                // Если FileUpload вернул массив (мультифайл) — берём первый
                                                if (is_array($uploaded)) {
                                                    $uploaded = $uploaded[0] ?? null;
                                                }

                                                // Livewire v3
                                                if ($uploaded instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                                                    $path = $uploaded->getRealPath();
                                                    if ($path && file_exists($path)) {
                                                        $raw = file_get_contents($path);
                                                    }
                                                }

                                                // Livewire v2 (совместимость)
                                                if (! $raw && $uploaded instanceof \Livewire\TemporaryUploadedFile) {
                                                    $path = $uploaded->getRealPath();
                                                    if ($path && file_exists($path)) {
                                                        $raw = file_get_contents($path);
                                                    }
                                                }

                                                // Если пришла строка пути
                                                if (! $raw && is_string($uploaded)) {
                                                    // Попытка через файловую систему Laravel
                                                    try {
                                                        if (Storage::exists($uploaded)) {
                                                            $raw = Storage::get($uploaded);
                                                        } elseif (Storage::disk('local')->exists($uploaded)) {
                                                            $raw = Storage::disk('local')->get($uploaded);
                                                        }
                                                    } catch (\Throwable $e) {
                                                        // игнорируем и пробуем локальные пути ниже
                                                    }

                                                    if (! $raw) {
                                                        $candidates = [
                                                            storage_path('app/' . ltrim($uploaded, '/\\')),
                                                            base_path($uploaded),
                                                            public_path($uploaded),
                                                            $uploaded,
                                                        ];
                                                        foreach ($candidates as $candidate) {
                                                            if ($candidate && @is_file($candidate)) {
                                                                $raw = @file_get_contents($candidate);
                                                                if ($raw !== false) { break; }
                                                            }
                                                        }
                                                    }
                                                }

                                                if ($raw !== null && $raw !== false) {
                                                    $raw = preg_replace("/^\xEF\xBB\xBF/", '', (string) $raw);
                                                    $payload = json_decode($raw, true);
                                                }
                                            }

                                            // Вариант 2: тело JSON из поля
                                            if (! $payload && ! empty($data['quiz_json'])) {
                                                $raw = (string) $data['quiz_json'];
                                                $raw = preg_replace("/^\xEF\xBB\xBF/", '', $raw ?? '');
                                                $payload = json_decode($raw, true);
                                            }

                                            if (json_last_error() !== JSON_ERROR_NONE || ! is_array($payload)) {
                                                throw ValidationException::withMessages([
                                                    'quiz_file' => 'Некорректный JSON: ' . json_last_error_msg() . '. Загрузите файл или вставьте JSON в поле.',
                                                ]);
                                            }

                                            $errors = self::validateImportPayload($payload);
                                            if ($errors) {
                                                throw ValidationException::withMessages([
                                                    'quiz_file' => "Ошибки структуры:\n" . implode("\n", $errors),
                                                ]);
                                            }

                                            DB::transaction(function () use ($lesson, $payload, $data) {
                                                $replace = (bool) ($data['replace_if_exists'] ?? false);
                                                $existing = $lesson->quizzes()->where('name', $payload['name'])->first();
                                                if ($existing) {
                                                    if (! $replace) {
                                                        throw ValidationException::withMessages([
                                                            'quiz_file' => "Тест с таким названием уже существует: '{$payload['name']}'. Отметьте галочку замены, если нужно перезаписать.",
                                                        ]);
                                                    }
                                                    $existing->questions()->each(function ($q) {
                                                        $q->questionOptions()->delete();
                                                    });
                                                    $existing->questions()->delete();
                                                    $existing->delete();
                                                }

                                                /** @var Quiz $quiz */
                                                $quiz = $lesson->quizzes()->create([
                                                    'name' => $payload['name'],
                                                    'description' => $payload['description'] ?? null,
                                                    'is_published' => (bool)($payload['is_published'] ?? false),
                                                    'passing_percentage' => $payload['passing_percentage'] ?? 80,
                                                    'max_attempts' => $payload['max_attempts'] ?? 3,
                                                ]);

                                                foreach ($payload['questions'] as $qi => $q) {
                                                    if (count($q['answers']) < 1) {
                                                        throw ValidationException::withMessages([
                                                            'quiz_file' => 'Вопрос #' . ($qi + 1) . ': требуется минимум 1 вариант ответа',
                                                        ]);
                                                    }
                                                    $hasCorrect = collect($q['answers'])->contains(fn ($a) => (bool)($a['correct'] ?? false));
                                                    if (! $hasCorrect) {
                                                        throw ValidationException::withMessages([
                                                            'quiz_file' => 'Вопрос #' . ($qi + 1) . ': отсутствует правильный вариант (correct=true)',
                                                        ]);
                                                    }

                                                    /** @var Question $question */
                                                    $question = $quiz->questions()->create([
                                                        'question_text' => trim($q['question_text']),
                                                        'hint' => $q['hint'] ?? null,
                                                        'more_info_link' => $q['more_info_link'] ?? null,
                                                    ]);

                                                    foreach ($q['answers'] as $a) {
                                                        $question->questionOptions()->create([
                                                            'option' => trim($a['option']),
                                                            'rationale' => $a['rationale'] ?? null,
                                                            'correct' => (bool)($a['correct'] ?? false),
                                                        ]);
                                                    }
                                                }
                                            });

                                            Notification::make()
                                                ->title('Тест импортирован')
                                                ->success()
                                                ->send();

                                            // Обновим страницу, чтобы отобразить новый тест в списке
                                            return redirect()->to(self::getUrl('edit', ['record' => $lesson->getKey()]));
                                        }),
                                ]),

                                Forms\Components\Repeater::make('quizzes')
                                    ->hiddenLabel()
                                    ->relationship('quizzes')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Название теста')
                                            ->required()
                                            ->columnSpanFull(),
                                        Forms\Components\Textarea::make('description')
                                            ->label('Описание теста')
                                            ->columnSpanFull(),
                                        Forms\Components\Checkbox::make('is_published')
                                            ->label('Опубликован'),
                                    ])
                                        ->itemLabel(function (array $state): ?string {
                                            if (empty($state['name'])) {
                                                return '';
                                            }
                                            return $state['name'];
                                        })
                                        ->columns()
                                    ->collapsible()
                                    ->collapsed()
                                    ->addActionLabel('Добавить тест')
                                    ->defaultItems(0),
                            ]),
                    ])
                    ->persistTab()
                    ->columnSpan('full')
                    ->activeTab(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Название')
                    ->limit(50)
                    ->tooltip(fn($state): string => $state)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('course.name')
                    ->label('Курс')
                    ->limit(30)
                    ->tooltip(fn($state): string => $state)
                    ->sortable(),
                Tables\Columns\TextColumn::make('position')
                    ->label('Позиция')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quizzes_count')
                    ->label('Тесты')
                    ->counts('quizzes'),
                Tables\Columns\ToggleColumn::make('is_published')
                    ->label('Опубликован'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('position')
            ->filters([
                SelectFilter::make('course')
                    ->label('Курс')
                    ->relationship('course', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('is_published')
                    ->label('Опубликован')
                    ->toggle()
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->hiddenLabel(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->persistSortInSession()
            ->persistSearchInSession()
            ->persistFiltersInSession();
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLessons::route('/'),
            'create' => Pages\CreateLesson::route('/create'),
            'edit' => Pages\EditLesson::route('/{record}/edit'),
        ];
    }

    protected static function validateImportPayload(array $d): array
    {
        $e = [];
        foreach (['name', 'questions'] as $k) {
            if (! array_key_exists($k, $d)) { $e[] = "Отсутствует поле '$k'"; }
        }
        if (empty($d['name'])) {
            $e[] = "Поле 'name' пустое";
        }
        if (empty($d['questions']) || ! is_array($d['questions'])) {
            $e[] = "Поле 'questions' отсутствует или не массив";
        } else {
            foreach ($d['questions'] as $i => $q) {
                if (! is_array($q)) { $e[] = "/questions/$i: не объект"; continue; }
                if (empty($q['question_text'])) { $e[] = "/questions/$i/question_text: пусто"; }
                if (empty($q['answers']) || ! is_array($q['answers'])) { $e[] = "/questions/$i/answers: отсутствуют"; continue; }
                foreach ($q['answers'] as $j => $a) {
                    if (! is_array($a)) { $e[] = "/questions/$i/answers/$j: не объект"; continue; }
                    if (empty($a['option'])) { $e[] = "/questions/$i/answers/$j/option: пусто"; }
                    if (! array_key_exists('correct', $a)) { $e[] = "/questions/$i/answers/$j/correct: отсутствует"; }
                }
            }
        }
        return $e;
    }
}
