function printFibonacci() {
    const n = document.getElementById('fibInput').value;
    let fib = [0, 1];

    for (let i = 2; i < n; i++) {
        fib[i] = fib[i - 1] + fib[i - 2];
    }

    document.getElementById('fibOutput').innerText = fib.slice(0, n).join(', ');

    console.log("Deret Fibonacci:");
    console.log(fib.slice(0, n).join(', '));
}