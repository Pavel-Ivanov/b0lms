<?php

namespace App\Filament\Intern\Widgets;

use App\Filament\Intern\Resources\TestResource;
use App\Models\Test;
use Filament\Tables\Actions\ViewAction;
use Filament\Infolists\Infolist;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class MyTestsResults extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->heading('Результаты тестов')
/*            ->defaultGroup('quiz.name')
            ->groupingDirectionSettingHidden()
            ->groups([
                Group::make('quiz.name')
                    ->titlePrefixedWithLabel(false)
                    ->collapsible(),
            ])*/

            ->query(
                    Test::query()
                        ->where('user_id', auth()->id())
                )
                ->columns([
                    Tables\Columns\TextColumn::make('quiz.name')
                        ->label('Тест'),
                    Tables\Columns\TextColumn::make('created_at')
                        ->label('Дата'),
                    Tables\Columns\TextColumn::make('questions_count')
                        ->label('Результат')
                        ->counts('questions')
                        ->formatStateUsing(function (Test $record) {
                            return  $record->result. '/' . $record->questions_count. ' (время: ' . (int)($record->time_spent / 60) . ':' . gmdate('s', $record->time_spent) .' минут)';
                        }),
                ])
            ->actions([
                ViewAction::make('viewDetails')
                    ->label('Результаты')
                    ->modalHeading('Результаты теста')
                    ->modalWidth('6xl')
                    ->infolist(function (\Filament\Tables\Contracts\HasTable $livewire, \App\Models\Test $record): Infolist {
                        return TestResource::infolist(Infolist::make()) // Вызываем статический метод infolist()
                        ->record($record);
                    }),
                ])
            ->paginated(10);
    }
}
