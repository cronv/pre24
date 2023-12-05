document.addEventListener('DOMContentLoaded', function () {
    const board = document.getElementById('board');
    const resetButton = document.getElementById('reset-button');
    const winChunk = 9;
    let currentPlayer = 'X';
    let gameBoard = new Array(winChunk).fill('');
    let gameActive = true;

    const generateWinPatterns = (size) => {
        const patterns = [];

        // Горизонтальные линии
        for (let i = 0; i < size; i++) {
            const horizontalLine = [];
            for (let j = 0; j < size; j++) {
                horizontalLine.push(i * size + j);
            }
            patterns.push(horizontalLine);
        }

        // Вертикальные линии
        for (let i = 0; i < size; i++) {
            const verticalLine = [];
            for (let j = 0; j < size; j++) {
                verticalLine.push(j * size + i);
            }
            patterns.push(verticalLine);
        }

        // Диагональные линии
        const diagonal1 = [];
        const diagonal2 = [];
        for (let i = 0; i < size; i++) {
            diagonal1.push(i * size + i);
            diagonal2.push(i * size + (size - 1 - i));
        }
        patterns.push(diagonal1, diagonal2);

        return patterns;
    };

    function renderBoard() {
        board.innerHTML = '';
        gameBoard.forEach((cell, index) => {
            const cellElement = document.createElement('div');
            cellElement.classList.add('cell');
            cellElement.textContent = cell;
            cellElement.addEventListener('click', () => handleCellClick(index));
            board.appendChild(cellElement);
        });
    }

    function handleCellClick(index) {
        if (gameBoard[index] === '' && gameActive) {
            gameBoard[index] = currentPlayer;
            renderBoard();
            checkGameStatus();
            togglePlayer();
        }
    }

    function checkGameStatus() {
        if (checkWinner()) {
            alert(`${currentPlayer} выиграл!`);
            gameActive = false;
        } else if (gameBoard.every(cell => cell !== '')) {
            alert('Ничья!');
            gameActive = false;
        }
    }

    let winPatterns = generateWinPatterns(Math.sqrt(winChunk));
    function checkWinner() {
        console.log(winPatterns)

        return winPatterns.some(pattern =>
            pattern.every(index => gameBoard[index] === currentPlayer)
        );
    }

    function togglePlayer() {
        currentPlayer = currentPlayer === 'X' ? 'O' : 'X';
    }

    function resetGame() {
        gameBoard = new Array(winChunk).fill('');
        gameActive = true;
        renderBoard();
    }

    resetButton.addEventListener('click', resetGame);

    // Инициализация игры
    renderBoard();
});
