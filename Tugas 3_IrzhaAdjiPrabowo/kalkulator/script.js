const readline = require('readline').createInterface({
    input: process.stdin,
    output: process.stdout
});

readline.question("Masukkan perhitungan (contoh: 10 + 5): ", (input) => {
    let [angka1, operator, angka2] = input.split(" ");
    angka1 = parseFloat(angka1);
    angka2 = parseFloat(angka2);

    const operasi = {
        '+': angka1 + angka2,
        '-': angka1 - angka2,
        '*': angka1 * angka2,
        '/': angka2 !== 0 ? angka1 / angka2 : "Tidak bisa dibagi 0",
        '%': angka1 % angka2
    };

    console.log(`Hasil: ${operasi[operator] !== undefined ? operasi[operator] : "Operator tidak valid"}`);
    readline.close();
});
