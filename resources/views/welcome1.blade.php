
<?php
$judul= "ini judul halaman";
?>
<x-halaman-layout :title="$judul" >
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Fugit blanditiis distinctio adipisci, provident totam rem aliquid vel facere suscipit. Adipisci, explicabo debitis? Quasi tempore rem consectetur ullam aut hic natus.</p>


        <x-slot name="tanggal"> 16 agustus 1234</x-slot>
       <x-slot name="penulis">rian</x-slot>
</x-halaman-layout>
   
<!-- <x-halaman-baru /> -->