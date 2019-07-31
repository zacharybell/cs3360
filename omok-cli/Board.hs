module Board ( mkBoard, mkPlayer, mkOpponent,
               size, row, column, mark,
               isEmpty, isMarked, marker,
               isFull, isWonBy, isDraw ,
               isGameOver, boardToStr, mkPlayer',
               Player(..), Place(..)) where

data Place = Place { x :: Int, y :: Int, p :: Player } deriving (Show)
data Player = Black | White | Empty deriving (Show, Eq)

mkBoard :: Int -> [Place]
mkBoard n = [ Place { x=i, y=j, p=Empty } | i <- [0..(n-1)], j <- [0..(n-1)] ]

mkPlayer :: Player
mkPlayer = Black

mkOpponent :: Player
mkOpponent = White

mkPlayer' :: Int -> Player
mkPlayer' p | p == 1 = Black | p == 2 = White | otherwise = Empty

size :: [Place] -> Int
size bd = floor $ sqrt $ fromIntegral $ length bd

row :: Int -> [Place] -> [Place]
row r bd = [ e | e <- bd, y e == r]

column :: Int -> [Place] -> [Place]
column c bd = [ e | e <- bd, x e == c]

mark :: Int -> Int -> [Place] -> Player -> [Place]
mark i j bd p = map f bd
    where f e = if x e == i && y e == j then Place {x=i,y=j,p=p} else e

isEmpty :: Int -> Int -> [Place] -> Bool
isEmpty i j bd = isMarkedBy i j bd Empty

isMarked :: Int -> Int -> [Place] -> Bool
isMarked i j bd = not $ isMarkedBy i j bd Empty

isMarkedBy :: Int -> Int -> [Place] -> Player -> Bool
isMarkedBy i j bd q = (marker i j bd) == q

marker :: Int -> Int -> [Place] -> Player
marker i j bd = p (head (filter f bd))
    where f e = if x e == i && y e == j then True else False

isFull :: [Place] -> Bool
isFull bd = 0 == length (filter (\e -> p e == Empty) bd)

isWonBy :: [Place] -> Player -> Bool
isWonBy bd q = isWonBy' bd bd q

isWonBy' :: [Place] -> [Place] -> Player -> Bool
isWonBy' _ [] _ = False
isWonBy' bd (h:t) q = win5 (build5 (x h) (y h) bd) q || isWonBy' bd t q

win5 :: [[Place]] -> Player -> Bool
win5 [] q = False
win5 (h:t) q = (win5' h q) || win5 t q

win5' :: [Place] -> Player -> Bool
win5' [] q = False
win5' [w] q = (q == p w)
win5' (h:t) q = (q == p h) && win5' t q

place :: Int -> Int -> [Place] -> Place
place i j bd = head (filter f bd) where f e = x e == i && y e == j

-- builds tuple pairs that could potentially contain win conditions
-- note: for (d1, d2) : (0,1) row, (1,0) col, (1, 1) tl to br, (1, -1) tr to bl
build5 :: Int -> Int -> [Place] -> [[Place]]
build5 x y bd = build5' x y bd (0,1) : build5' x y bd (1,0) : build5' x y bd (1,1) : build5' x y bd (1, -1) : []

build5' :: Int -> Int -> [Place] -> (Int,Int) -> [Place]
build5' x y bd d =
    let s = size bd
        r = [ place a b bd | a <- [(x-4)..(x+4)], b <- [(y-4)..(y+4)], (fst d * (a - x)) == (snd d * (b - y)), 0 <= a, 0 <= b, a < s, b < s ]
    in if length r >= 5 then (take 5 r) else []

isDraw :: [Place] -> Bool
isDraw bd = not ((isWonBy bd Black) || (isWonBy bd White)) && isFull bd

isGameOver :: [Place] -> Bool
isGameOver bd = (isWonBy bd Black) || (isWonBy bd White) || isFull bd

boardToStr :: (Player -> Char) -> [Place] -> String
boardToStr f bd =
    let s = size bd
    in " x " ++ unwords [ show ((a+1) `mod` 10) | a <- [0..(s-1)] ] ++ "\n"
    ++ "y " ++ concat (replicate (2*s) "-") ++ "\n"
    ++ concat [ show ((a+1) `mod` 10) ++ "| " ++ unwords [ [f (p e)] | e <- r ] ++ "\n" | a <- [0..(s-1)], let r = row a bd ]
