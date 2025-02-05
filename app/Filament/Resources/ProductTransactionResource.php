<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductTransactionResource\Pages;
use App\Filament\Resources\ProductTransactionResource\RelationManagers;
use App\Models\External;
use App\Models\Package;
use App\Models\ProductTransaction;
use App\Models\PromoCode;
use Faker\Provider\Text;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use function Laravel\Prompts\select;

class ProductTransactionResource extends Resource
{
    protected static ?string $model = ProductTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                    Forms\Components\Fieldset::make('Payment Information')
                ->schema([
                        //Make a Grid 2 Column
                        Forms\Components\Grid::make(2)->extraAttributes(['class' => 'w-4/4'])
                        ->schema([
                            //Get & Make Package Selection For Product Transactions
                            Forms\Components\Select::make('package_id')
                            ->relationship('package', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {

                                $package = Package::find($state);
                                $price = $package ? $package->price : 0;
                                $quantity = $get('quantity') ?? 1;
                                $eprice = $get('eprice');
                                $subTotalAmount = $price + $eprice * $quantity;

                                $set('price', $price);
                                $set('sub_total_amount', $subTotalAmount);

                                $discount = $get('discount_amount') ?? 0;
                                $grandTotalAmount = $subTotalAmount - $discount;
                                $set('grand_total_amount', $grandTotalAmount);
                            }),
                            //Get External Id Form For Product Transaction
                            Forms\Components\Select::make('external_id')
                                ->relationship('external', 'name')
                                ->searchable()
                                ->preload()
                                ->live()
                                ->afterStateUpdated(function ($state, callable $get, callable $set){

                                    $external = External::find($state);
                                    $price = $get('price');
                                    $eprice = $external ? $external->eprice : 0;
                                    $quantity = $get('quantity') ?? 1;
                                    $subTotalAmount = $price + $eprice * $quantity;

                                    $set('eprice', $eprice);
                                    $set('sub_total_amount', $subTotalAmount);

                                    $discount = $get('discount_amount') ?? 0;
                                    $grandTotalAmount = $subTotalAmount - $discount;
                                    $set('grand_total_amount', $grandTotalAmount);
                                }),

                            //Quantity Form On Product Transaction
                            Forms\Components\TextInput::make('quantity')
                                ->required()
                                ->numeric()
                                ->prefix('Qty')
                                ->live()
                                ->afterStateUpdated(function ($state, callable $get, callable $set){
                                    $price = $get('price');
                                    $eprice = $get('eprice');
                                    $quantity = $state;
                                    $subTotalAmount = $price + $eprice * $quantity;

                                    $set('sub_total_amount', $subTotalAmount);

                                    $discount = $get('discount_amount') ?? 0;
                                    $grandTotalAmount = $subTotalAmount - $discount;
                                    $set('grand_total_amount', $grandTotalAmount);
                                }),

                            //Make & Get Forms For Promo Code Function
                            Forms\Components\Select::make('promo_code_id')
                            ->relationship('promoCode', 'code')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $get, callable $set){
                                $subTotalAmount = $get('sub_total_amount');
                                $promoCode = PromoCode::find($state);
                                $discount = $promoCode ? $promoCode->discount_amount : 0;

                                $set('discount_amount', $discount);

                                $grandTotalAmount = $subTotalAmount - $discount;
                                $set('grand_total_amount', $grandTotalAmount);
                            }),

                            Forms\Components\TextInput::make('sub_total_amount')
                            ->required()
                            ->readOnly()
                            ->numeric()
                            ->prefix('IDR'),

                            Forms\Components\TextInput::make('grand_total_amount')
                            ->required()
                            ->readOnly()
                            ->numeric()
                            ->prefix('IDR'),

                            Forms\Components\TextInput::make('discount_amount')
                            ->required()
                            ->numeric()
                            ->prefix('IDR'),

                        ]),
                    Forms\Components\Fieldset::make('Customer Information')
                        ->schema([
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\TextInput::make('name')
                                        ->required()
                                        ->maxLength(255),

                                    Forms\Components\TextInput::make('phone')
                                        ->required()
                                        ->maxLength(255),

                                    Forms\Components\TextInput::make('email')
                                        ->required()
                                        ->maxLength(255),

                                    Forms\Components\Textarea::make('address')
                                        ->required()
                                        ->rows(5),

                                    Forms\Components\TextInput::make('city')
                                        ->required()
                                        ->maxLength(255),

                                    Forms\Components\TextInput::make('post_code')
                                        ->required()
                                        ->maxLength(255),
                                ]),
                        ]),

                Forms\Components\Fieldset::make('Payment Information')
                    ->schema([

                        Forms\Components\TextInput::make('book_trx_id')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\ToggleButtons::make('is_paid')
                            ->label('Apakah Sudah Membayar')
                            ->boolean()
                            ->grouped()
                            ->icons([
                                true => 'heroicon-o-pencil',
                                false => 'heroicon-o-clock',
                            ])

                            ->required(),

                        Forms\Components\FileUpload::make('proof')
                            ->image()
                            ->required(),
                ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\ImageColumn::make('package.thumbnail'),

                Tables\Columns\TextColumn::make('name')
                ->searchable(),

                Tables\Columns\TextColumn::make('book_trx_id')
                ->searchable()
                ->label('Kode Transaksi'),

                Tables\Columns\IconColumn::make('is_paid')
                ->boolean()
                ->trueColor('success')
                ->falseColor('danger')
                ->trueIcon('heroicon-o-check-circle')
                ->falseIcon('heroicon-o-x-circle')
                ->label('Terverifikasi'),
            ])
            ->filters([
                //
                Tables\Filters\SelectFilter::make('package_id')
                    ->label('Package')
                    ->relationship('package', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),

                Tables\Actions\Action::make('approve')
                ->label('Approve')
                ->action(function(ProductTransaction $record){
                    $record->is_paid =true;
                    $record->save();

                    Notification::make()
                    ->title('Order Aproved')
                    ->success()
                    ->body('The Order has been successfully approved.')
                    ->send();
                })
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn (ProductTransaction $record) => !$record->is_paid),
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
            'index' => Pages\ListProductTransactions::route('/'),
            'create' => Pages\CreateProductTransaction::route('/create'),
            'edit' => Pages\EditProductTransaction::route('/{record}/edit'),
        ];
    }
}
