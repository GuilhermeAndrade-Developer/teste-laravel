<!DOCTYPE html>
<html>
<head>
    <title>Livro Emprestado</title>
</head>
<body>
    <h1>Olá, {{ $user->name }}</h1>
    <p>O livro "{{ $book->title }}" foi emprestado para você.</p>
    <p>Data do Empréstimo: {{ $book->loan_date }}</p>
    <p>Por favor, devolva o livro dentro do prazo estipulado.</p>
</body>
</html>
