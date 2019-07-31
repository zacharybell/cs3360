package omok.base;

import java.awt.BorderLayout;
import java.awt.Color;
import java.awt.Dimension;
import java.awt.FlowLayout;
import java.awt.GridLayout;
import java.awt.event.ActionEvent;

import javax.swing.BorderFactory;
import javax.swing.JButton;
import javax.swing.JDialog;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JOptionPane;
import javax.swing.JPanel;

import omok.model.Board;
import omok.model.Player;

/**
* A dialog for playing omok games. Omok (meaning "five pieces") is
* a strategy board game typically played with go pieces (black and
* white stones). It can also be played with a paper and pencils,
* for stones, once placed, are not allowed to be moved or removed
* from the board. The two players alternate in placing a stone of
* their color on an empty intersection, and the goal of the game is
* to place one's stones in a row of five consecutive intersections 
* vertically, horizontally, or diagonally. The winer is the first player
* to get an unbroken row of five stones.
*
* @author Yoonsik Cheon
*/
@SuppressWarnings("serial")
public class OmokDialog extends JDialog {

    /** Default dimension of the dialog. */
    private final static Dimension DEFAULT_SIZE = new Dimension(368, 440);
    
    /** To start a new game. */
    private final JButton playButton = new JButton("Play");
      
    /** Message bar to display various messages. */
    private final JLabel msgBar = new JLabel();

    /** Omok board consisting of nxn intersections (or places) on which
     * stones can be placed. */
    private final Board board;
       
    /** The player of this game. */
    private Player player;
    
    /** Create an omok dialog. */
    public OmokDialog() {
    	this(DEFAULT_SIZE);
    }
    
    /** Create an omok dialog of the given screen dimension. */
    public OmokDialog(Dimension dim) {
        super((JFrame) null, "Omok");
        setSize(dim);
        board = new Board();
        player = new ColorPlayer("Black", Color.BLACK);
        configureUI(board);
        setLocationRelativeTo(null);
        setDefaultCloseOperation(DISPOSE_ON_CLOSE);
        setVisible(true);
        setResizable(false);
    }
	   
    /** Configure UI. */
    private void configureUI(Board board) {
        setLayout(new BorderLayout());
        add(makeControlPanel(), BorderLayout.NORTH);
        JPanel panel = new JPanel();
        panel.setBorder(BorderFactory.createEmptyBorder(10,20,10,20));
        panel.setLayout(new GridLayout(1,1));
        panel.add(createBoardPanel(board));
        add(panel, BorderLayout.CENTER);
    }
    
    private BoardPanel createBoardPanel(Board board) {
        return new BoardPanel(board, this::makeMove);
    }
      
    /**
     * Create a control panel consisting of a play button and
     * a message bar.
     */
    private JPanel makeControlPanel() {
        JPanel content = new JPanel(new BorderLayout());
        JPanel buttons = new JPanel(new FlowLayout(FlowLayout.LEFT));
        buttons.setBorder(BorderFactory.createEmptyBorder(5,15,0,0));
        buttons.add(playButton);
        playButton.setFocusPainted(false);
        playButton.addActionListener(this::playButtonClicked);
        content.add(buttons, BorderLayout.NORTH);
        msgBar.setText(player.name() + "' turn.");
        msgBar.setBorder(BorderFactory.createEmptyBorder(5,20,0,0));
        content.add(msgBar, BorderLayout.SOUTH);
        return content;
    }

    /** Show the given string on the message bar. */
    private void showMessage(String msg) {
        msgBar.setText(msg);
    }
    
    /** To be called when the play button is clicked. This method
     * first prompts the user for a confirmation and then proceed
     * accordingly -- starts a new game or quit and return to the
     * current game.*/
    private void playButtonClicked(ActionEvent event) {
        if (JOptionPane.showConfirmDialog(OmokDialog.this, 
                "Play a new game?", "Omok", JOptionPane.YES_NO_OPTION)
                    == JOptionPane.YES_OPTION) {
            startNewGame();
        }
    }
           
    /** Start a new game. */
    private void startNewGame() {
        board.clear();
        showMessage(player.name() + "'s turn.");
        repaint();
    }
     
    /** Place a stone of the given player at the specified place. */
    private void makeMove(Board.Place place) {
        board.placeStone(place.x, place.y, player);
        repaint();
    }
          
    public static void main(String[] args) {
        new OmokDialog();
    }

}
