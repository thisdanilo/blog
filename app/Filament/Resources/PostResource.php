<?php

namespace App\Filament\Resources;

use App\Models\Category;
use Closure;
use App\Models\Post;
use Filament\Tables;
use Illuminate\Support\Str;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\MultiSelect;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Forms\Components\MarkdownEditor;
use App\Filament\Resources\PostResource\Pages;
use Filament\Forms\Components\BelongsToSelect;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationLabel = 'Postagens';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('image')->collection('posts')->label('Imagem'),
                        TextInput::make('title')
                            ->reactive()
                            ->afterStateUpdated(function (Closure $set, $state) {
                                $set('slug', Str::slug($state));
                            })->required()->label('Título'),
                        TextInput::make('slug')->required()->unique(table: Post::class),
                        Toggle::make('is_published')
                            ->onIcon('heroicon-s-lightning-bolt')
                            ->offIcon('heroicon-s-user')
                            ->required()->label('Publicado'),
                        BelongsToSelect::make('category_id')
                            ->relationship(
                                'category',
                                'name'
                            )->required()->label('Categoria'),
                        MultiSelect::make('tag_id')
                            ->relationship('tags', 'name')->required()
                    ])->columns(3),
                Card::make()
                    ->schema([
                        MarkdownEditor::make('description')->required()->label('Descrição'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('imagem')->collection('posts')->label('Imagem'),
                TextColumn::make('title')->sortable()->searchable()->label('Título'),
                TextColumn::make('slug')->sortable(),
                TextColumn::make('category.name')->sortable()->searchable()->label('Categoria'),
                TextColumn::make('tags.name')->sortable()->searchable(),
                BooleanColumn::make('is_published')
                    ->trueIcon('heroicon-o-badge-check')
                    ->falseIcon('heroicon-o-x-circle')
                    ->sortable()
                    ->label('Publicado')
            ])
            ->filters([
                Filter::make('Publicado')
                    ->query(fn (Builder $query): Builder => $query->where('is_published', true)),
                Filter::make('Não Publicado')
                    ->query(fn (Builder $query): Builder => $query->where('is_published', false))
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
