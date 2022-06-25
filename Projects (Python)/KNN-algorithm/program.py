import random
import time
import math
import matplotlib.pyplot as plt
from copy import deepcopy

COLORS = ['\33[31m', '\33[35m', '\33[33m', '\33[36m'] # colors in the console
MAPCOLORS = {"red": "lightcoral", "green": "seagreen", "blue": "turquoise", "purple": "mediumpurple"} # colors in the graph
RESET = '\033[0m'

RED = [(-4500, -4400), (-4100, -3000), (-1800, -2400), (-2500, -3400), (-2000, -1400)] # default red points
GREEN = [(4500, -4400), (4100, -3000), (1800, -2400), (2500, -3400), (2000, -1400)] # default green points
BLUE = [(-4500, 4400), (-4100, 3000), (-1800, 2400), (-2500, 3400), (-2000, 1400)] # default blue points
PURPLE = [(4500, 4400), (4100, 3000), (1800, 2400), (2500, 3400), (2000, 1400)] # default purple points
MAP = [] # the whole map of already inserted points

class Point:
    def __init__(self, x, y, color):
        self.x = x # current X axis
        self.y = y # current Y axis
        self.color = color # classified new color

def findMax(array):
    maximum = array[0] # marking the first distance as the longest one
    for item in array: # looking for the longer distance
        if item[0] > maximum[0]: # if we found it
            maximum = item # change the maximum

    return maximum

def findMainColor(neighbors):
    array = [[0, 'red'], [0, 'green'], [0, 'blue'], [0, 'purple']]
    for neighbor in neighbors: # count how many times we have each colour
        if neighbor[1] == 'red':
            array[0][0] += 1
        if neighbor[1] == 'green':
            array[1][0] += 1
        if neighbor[1] == 'blue':
            array[2][0] += 1
        if neighbor[1] == 'purple':
            array[3][0] += 1

    return findMax(array)[1] # return the main color

def classify(x, y, k):
    nearestPoints = [] # array of the nearest neighbors
    for neighbor in MAP:
        distance = math.dist((x, y), (neighbor.x, neighbor.y)) # counting the distance between the points

        if len(nearestPoints) < k: # if we have checked less neighbors that the 'k'
            nearestPoints.append((distance, neighbor.color)) # add them to the array
            maximum = findMax(nearestPoints) # find the furthest point between the neighbors
        else:
            if maximum[0] > distance: # if we have a closer neighbors
                nearestPoints.remove(maximum) # forget about an old one
                nearestPoints.append((distance, neighbor.color)) # remember the new neighbor
                maximum = findMax(nearestPoints)  # find new furthest point between the neighbors

    return findMainColor(nearestPoints)

def insertPoint(arrayOfCoordinates, coordinate, old_color, k):
    new_color = classify(arrayOfCoordinates[coordinate][0], arrayOfCoordinates[coordinate][1], k) # classify a point of exact color
    MAP.append(Point(arrayOfCoordinates[coordinate][0], arrayOfCoordinates[coordinate][1], new_color)) # add it to the whole map
    if new_color == old_color: # if we classified the new inserted point right - enlarge the % of success
        return 1
    return 0

def compareMap(numberOfPoints, map, red, green, blue, purple, k):
    global MAP
    MAP = deepcopy(map) # reseting the map of default points
    success_counter = 0 # count how many colors matches

    for coordinate in range(0, numberOfPoints): # for each generated point of each color - classify it
        # if the new color match with the old one - add 1 to the success_counter
        success_counter += insertPoint(red, coordinate, 'red', k)
        success_counter += insertPoint(green, coordinate, 'green', k)
        success_counter += insertPoint(blue, coordinate, 'blue', k)
        success_counter += insertPoint(purple, coordinate, 'purple', k)

    return success_counter

def generatePoint(points, min_X, max_X, min_Y, max_Y, color):
    x = random.randint(min_X, max_X) # generating the X axis of the point
    y = random.randint(min_Y, max_Y) # generating the Y axis of the point

    while (x, y) in points.keys(): # if this point was already generated - generate a new one and check it again
        x = random.randint(min_X, max_X)
        y = random.randint(min_Y, max_Y)

    points[(x, y)] = color # add it to the dictionary of all points

def generate(numberOfPoints):
    points = {} # all points that will be inserted
    # adding default points to the whole dictionary of points
    for point in range(0, len(RED)): # the red ones
        points[RED[point]] = 'RED'
    for point in range(0, len(GREEN)): # the green ones
        points[GREEN[point]] = 'GREEN'
    for point in range(0, len(BLUE)): # the blue ones
        points[BLUE[point]] = 'BLUE'
    for point in range(0, len(PURPLE)): # the purple ones
        points[PURPLE[point]] = 'PURPLE'

    for _ in range(0, numberOfPoints):
        for color in ('red', 'green', 'blue', 'purple'):
            probability = random.random() # generating a point with the 99% probability
            if probability < 0.99:
                if color == 'red':
                    generatePoint(points, -5000, 499, -5000, 499, color) # adding the new red point
                if color == 'green':
                    generatePoint(points, -499, 5000, -5000, 499, color) # adding the new green point
                if color == 'blue':
                    generatePoint(points, -5000, 499, -499, 5000, color) # adding the new blue point
                if color == 'purple':
                    generatePoint(points, -499, 5000, -499, 5000, color) # adding the new purple point
            else: # generate a random point in the map
                generatePoint(points, -5000, 5000, -5000, 5000, color)

    # dividing into different color groups
    red = [] # all coordinates of red points
    green = [] # all coordinates of green points
    blue = [] # all coordinates of blue points
    purple = [] # all coordinates of purple points
    map = [] # array full of default points
    for coordinates, color in points.items():
        if color == 'red':
            red.append(coordinates)
        elif color == 'green':
            green.append(coordinates)
        elif color == 'blue':
            blue.append(coordinates)
        elif color == 'purple':
            purple.append(coordinates)
        else:
            map.append(Point(coordinates[0], coordinates[1], color.lower())) # creating a point and adding it to the map

    return map, red, green, blue, purple

def test(bigness):
    K = (1, 3, 7, 15) # all possible values of k
    axis = [-5000, -4000, -3000, -2000, -1000, 0, 1000, 2000, 3000, 4000, 5000] # numerated axis

    for numberOfPoints in bigness: # number of points that have to be generated
        map, red, green, blue, purple = generate(numberOfPoints // 4)  # generate all points in the map
        print("\nAll %d points are already generated."%(numberOfPoints))

        for colorIndex, k in enumerate(K):
            print(COLORS[colorIndex], "\nGenerating a map for a k =", k)
            start = time.time()
            success_counter = compareMap(numberOfPoints//4, map, red, green, blue, purple, k) # start a comparison
            totalTime = time.time() - start # check the duration time
            successPercentage = (success_counter / numberOfPoints) * 100 # count the success
            print("Total success is:      %.2f %%" % (successPercentage))
            print("Total time is:         %.3f s"%(totalTime), RESET)
            plt.xlim(left = -5000, right = 5000) # adding the limits to the picture
            plt.ylim(bottom = -5000, top = 5000) # adding the limits to the picture
            plt.xticks(axis)
            plt.yticks(axis)
            plt.scatter([point.x for point in MAP], [point.y for point in MAP], c=[MAPCOLORS[point.color] for point in MAP])
            plt.title("K = %d\nNumber of points = %d, Total time: %.3fs, Success: %.2f%%"%(k, numberOfPoints, totalTime, successPercentage))
            plt.savefig("%d_%d.jpg"%(k, numberOfPoints))
            plt.clf()

            f = open("results_test.txt", "a")  # all data will be written in a results_test.txt
            f.write("%d %d %.3f %.2f\n" % (numberOfPoints, k, totalTime, successPercentage))
            f.close()

def main():
    choice = int(input("\nPrint 1 to perform the test or 2 to make a classification with your own parameters:    "))

    if choice == 1:
        test([1000, 20000, 40000, 60000]) # test all of this sizes
    elif choice == 2:
        print("Write how many points you want to insert (any positive number divisible by 4):")
        numberOfPoints = int(input())
        if numberOfPoints%4 == 0:
            test([numberOfPoints])
        else:
            print("You have put wrong quantity of points!")
    else:
        print("You have put the wrong number!")

main()