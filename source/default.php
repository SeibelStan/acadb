{{header}}

{index = explode("\n", file_get_contents('build-index.txt'));}

<ul>
@foreach $index as $row
    {row = preg_replace('/\*$/', '', $row)}
    <li><a href="/{ROOT}{row}">{row}</a>
@eforeach
</ul>

{{footer}}