import System.IO
import System.Random
import Board

playerToChar :: Player -> Char
playerToChar p | p == Black = 'O' | p == White = 'X' | otherwise = '.'

getPlayer :: Player -> Player
getPlayer p | p == Black = White | p == White = Black | otherwise = Black

readXY :: [Place] -> Player -> IO (Int, Int)
readXY bd p = do
  putStrLn (([playerToChar p]) ++ "\'s turn: enter x y (1-15 or -1 to quit)?")
  line <- getLine
  let a = parse line in
    if length a == 0 then putError bd p line else
      let (x,s) = head a in
      if (x == -1) then return (-1, 0) else
      if ( (x <= 0) || (x > 15) || (length s == 0) )
      then putError bd p line else
        let b = parse s in
          if length b == 0 then putError bd p line else
            let (y,_) = head b in
              if ( y > 0 && y <= 15 )
              then return (x - 1, y - 1)
              else putError bd p line
  where parse l = reads l :: [(Int, String)]
        putError bd p l = do
          putStrLn ("Invalid input: " ++ l)
          readXY bd p

start :: [Place] -> Player -> IO ()
start bd p = do
  putStr (boardToStr (playerToChar) bd)
  if isGameOver bd
    then
      if isWonBy bd (getPlayer p)
      then do
        putStrLn ([playerToChar (getPlayer p)] ++ " wins!")
        return ()
      else do
        putStrLn "Draw!"
        return ()
    else do
      (x,y) <- (readXY bd p)
      if x == -1
        then do
          putStrLn "Bye, Felicia! (push any key to quit)"
          _ <- getChar
          return ()
        else
          if isEmpty x y bd
          then
            let mbd = (mark x y bd p) in
            start mbd (getPlayer p)
          else do
            putStrLn "Already marked! (push any key to continue)"
            _ <- getChar
            start bd p
  return ()


main :: IO ()
main = do
  let p = getPlayer Empty
      b = mkBoard 15
      in start b p
