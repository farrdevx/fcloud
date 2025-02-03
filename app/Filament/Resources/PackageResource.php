<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PackageResource\Pages;
use App\Filament\Resources\PackageResource\RelationManagers;
use App\Models\Package;
use Filament\Forms;
use Filament\Tables\Columns\IconColumn;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PackageResource extends Resource
{
    protected static ?string $model = Package::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make('✨ Details ✨')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nama')
                    ->required()
                    ->maxLength(255),
                    Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('IDR'),
                    Forms\Components\FileUpload::make('thumbnail')
                    ->image()
                    ->required(),
                    Forms\Components\Repeater::make('photos')
                    ->relationship('photos')
                    ->schema([
                        Forms\Components\FileUpload::make('photo')
                        ->required(),
                    ]),
                ]),

                Fieldset::make('Additional')
                ->schema([
                    Forms\Components\Textarea::make('about')
                    ->required(),
                    Forms\Components\Select::make('is_active')
                        ->label('💎Visible💎')
                        ->options([
                        true => 'Visible',
                        false => 'Hidden',
                    ])
                    ->required(),
                ]),

                Forms\Components\Select::make('category_id')
                ->relationship('category', 'name')
                ->searchable()
                ->preload()
                ->required(),

                Forms\Components\TextInput::make('stock')
                ->required()
                ->numeric()
                ->prefix('Qty'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('name')
                ->searchable(),
                Tables\Columns\TextColumn::make('category.name'),

                Tables\Columns\ImageColumn::make('thumbnail'),

                Tables\Columns\IconColumn::make('is_active')
                ->boolean()
                ->trueColor('success')
                ->falseColor('danger')
                ->trueIcon('heroicon-o-check-circle')
                ->falseIcon('heroicon-o-x-circle')
                ->label('Visible'),
            ])
            ->filters([
                //
                SelectFilter::make('category_id')
                ->label('Category')
                ->relationship('category', 'name'),

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListPackages::route('/'),
        ];
    }
}
