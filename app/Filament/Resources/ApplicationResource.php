<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApplicationResource\Pages;
use App\Filament\Resources\ApplicationResource\RelationManagers;
use App\Models\Application;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ApplicationResource extends Resource
{
    protected static ?string $model = Application::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->disabled()->required(),
                TextInput::make('dob')->label("age")->disabled()->required(),
                TextInput::make('gender')->disabled()->required(),
                TextInput::make('phone')->disabled()->required(),
                TextInput::make('email')->disabled()->required(),
                TextInput::make('governoment')->disabled()->required(),
                TextInput::make('educational_qualification')->disabled()->required(),
                TextInput::make('languages')->disabled()->required(),

                // Conditionally disable 'admin_rate' if admin_id is set
                TextInput::make('admin_rate')
                    ->numeric()
                    ->required()
                    ->disabled(function ($get) {
                        return $get('admin_id') !== null; // Disable if admin_id is present
                    }),

                // Conditionally disable 'is_approved' if admin_id is set
                Select::make('is_approved')
                    ->label('Status')
                    ->options([
                        1 => 'Approved',
                        0 => 'Not Approved',
                    ])
                    ->required()
                    ->disabled(function ($get) {
                        return $get('admin_id') !== null; // Disable if admin_id is present
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('user.photo')
                ->getStateUsing(function ($record) {
                    return $record->user && $record->user->photo
                        ? asset('storage/' . $record->user->photo)
                        : asset('default-avatar-icon-of-social-media-user-vector.jpg'); // Default photo if user photo is missing
                }),
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('email')->sortable()->searchable(),
                TextColumn::make('gender')->sortable()->searchable(),
                TextColumn::make('admin_rate')->sortable(),
                TextColumn::make('admin.name')->label('Reviewed by')->sortable(),
                TextColumn::make('total_rates')->label('Total points')->getStateUsing(function ( $record) {
                    return $record->rates->sum('rate');
                }),
                TextColumn::make('created_at')->sortable()->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->icon('heroicon-s-eye')
                ->label('show')
                ,
                Tables\Actions\DeleteAction::make(), // Add the DeleteAction here
                // Tables\Actions\Action::make('download_video')
                // ->extraAttributes([
                //     'target' => '_blank',
                //     'download' => 'download',
                //     'random_shit' => 'this_works',
                // ])

                // ->label('Download Video')

                // ->url(function ($record) {

                //     return ($record->video);

                // })
                // ->icon('heroicon-s-arrow-down-tray'),
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
            'index' => Pages\ListApplications::route('/'),
            'create' => Pages\CreateApplication::route('/create'),
            'edit' => Pages\EditApplication::route('/{record}/edit'),
        ];
    }
}
