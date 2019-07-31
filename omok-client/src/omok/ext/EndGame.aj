package omok.ext;

import omok.base.OmokDialog;
import omok.model.Board;
import omok.model.Player;

/**
 * <h2>EndGame Aspect</h2>
 *
 * <p>This aspect is responsible for ending the omok game on a win or draw. It does this
 * by creating a pointcut for each stone placement and checking if the game is full or
 * won. If it detects these conditions it will display the game outcome and highlight a
 * winner if one exists.</p>
 *
 * <h3>Requirements:</h3>
 * <ul>
 *  <li>end game</li>
 *  <li>display outcome</li>
 *  <li>highlight winning sequence</li>
 *  <li>remove win prompt</li>
 * </ul>
 *
 * @author Zachary J Bell
 * @see OmokDialog
 * @see Board
 * @version 1.0-SNAPSHOT
 */
public privileged aspect EndGame {

    /// POINTCUTS
    pointcut OmokDialog(OmokDialog d) : this(d) && initialization(OmokDialog.new());
    pointcut makeMove(OmokDialog d) : this(d) && execution(void OmokDialog.makeMove(..));
    pointcut playButtonClicked(OmokDialog d) : this(d) && execution(void OmokDialog.playButtonClicked(ActionEvent));

    /*
     * Prevents the game from continuing after a game over condition is reached by blocking makeMove(..).
     */
    void around(OmokDialog d) : makeMove(d) {
        if(!d.board.isGameOver()) {
            proceed(d);
        }
    }

    /*
     * Blocks the dialog box from appearing after the game is won and the play button is clicked.
     */
    void around(OmokDialog d) : playButtonClicked(d) {
        if (d != null && d.board != null && d.board.isGameOver()) {
            d.startNewGame();
            d.board.clearBestMoves();
        }
        else proceed(d);
    }

    /*
     * Adds a BoardChangeListener that updates OmokDialog's message when a game over condition is reached.
     */
    after (OmokDialog d) : OmokDialog(d) {
        d.board.addBoardChangeListener(new Board.BoardChangeAdapter() {
            @Override
            public void gameOver(Player winner) {
                if (winner == null) {
                    d.showMessagePublic("Draw!");
                }
                else {
                    d.showMessagePublic(winner.name() + " wins!");
                }
            }
        });
    }
}

