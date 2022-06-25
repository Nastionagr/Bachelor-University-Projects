import random
import time
import matplotlib.pyplot as plt
import matplotlib
matplotlib.use('tkagg')

# constant values
max_generations = 1000
population_size = 150
mutation_min = 0.01
mutation_max = 0.3
crossover_rate = 0.99
parent_selection = 1  # 1 - Tournament Selection        2 - Roulette Wheel Selection

colors = ['\33[31m', '\33[33m', '\33[34m', '\33[35m', '\33[36m', '\33[91m', '\33[92m', '\33[93m', '\33[94m', '\33[95m', '\33[96m', '\33[97m', '\33[32m']
creset = '\033[0m'
drawAnimation = True

def createMap(size_x, size_y, stones):
    arr = [[0 for i in range(size_x)] for i in range(size_y)] #creating 2D array for the map
    for stone_x, stone_y in stones:
        arr[stone_y][stone_x] = -1 #mark stones in the array as '-1'
    return arr

def countFitness(map, size_x, size_y):
    count = 0
    for y in range(size_y):
        for x in range(size_x):
            if map[y][x] > 0: #counts the items, where the way was buit (and it isn't a stone)
                count += 1
    return count

def is_in_Map(x, y, size_x, size_y): #check if the item (x, y) is in map
    return True if ((x >= 0 and x < size_x) and (y >= 0 and y < size_y)) else False

def checkPosition(map, size_x, size_y, x, dif_x, y, dif_y):
    if is_in_Map(x + dif_x, y + dif_y, size_x, size_y):
        if map[y + dif_y][x + dif_x] == 0: #if we can keep on moving in the prefered direction of the gene
            return (dif_x, dif_y)
        else: #if there is smth on the way
            return True
    return False

def evaluateChromosome(size_x, size_y, Map, chromosome, returnMap=False):
    for rowI in range(size_y):
        for colI in range(size_x):
            if Map[rowI][colI] > 0:
                Map[rowI][colI] = 0 #reseting the map

    if returnMap and drawAnimation:
        for rowI in range(size_y):
            for colI in range(size_x):
                if Map[rowI][colI] < 0: #printing the stones at first
                    ax = plt.gca()
                    text = ax.text(colI + 0.5, size_y - 1 - rowI + 0.5, "K" if Map[rowI][colI] == -1 else Map[rowI][colI], ha="center", va="center", color="gray", fontweight="bold")

    stepCounter = 1
    for genX, genY, genDirec in chromosome: #for each gene in chromosome do...
        if Map[genY][genX] != 0:
            continue

        #if gene starts from the left side, go to the right (1), from the right side - go to the left (-1), else don't move on OX (0)
        diffX = 1 if genX == 0 else -1 if genX == size_x - 1 else 0
        #if gene starts from the top side, go down (1), from the bottom side - go up (-1), else don't move on OY (0)
        diffY = 0 if genX == 0 or genX == size_x - 1 else 1 if genY == 0 else -1

        tempX, tempY = genX, genY
        moves = [] #array of the movement coordinates

        while 1:
            Map[tempY][tempX] = stepCounter  #the number of the current way

            moves.append((tempX, tempY))

            if is_in_Map(tempX + diffX, tempY + diffY, size_x, size_y) == False:  #if monk is on the border -> move to next gene
                break

            if Map[tempY + diffY][tempX + diffX] == 0:  #harvest the next field
                tempY += diffY
                tempX += diffX
                continue

            if diffY == 0:  #horizontal movement
                possibleMoves = [checkPosition(Map, size_x, size_y, tempX, 0, tempY, -diffX), checkPosition(Map, size_x, size_y, tempX, 0, tempY, diffX)]
            else:  #vertical movement
                possibleMoves = [checkPosition(Map, size_x, size_y, tempX, diffY, tempY, 0), checkPosition(Map, size_x, size_y, tempX, -diffY, tempY, 0)]

            legit = sum(type(item) == type(()) for item in possibleMoves)
            if legit == 1: #if it is just one possible way
                for itemIndex in range(2):
                    if type(possibleMoves[itemIndex]) == type(()):
                        diffX, diffY = possibleMoves[itemIndex] #change diffX, diffY
            elif legit == 2: #if both directions are possible
                if genDirec == "l": #if we prefer going to the left
                    diffX, diffY = possibleMoves[0]
                else: #if we prefer going to the right
                    diffX, diffY = possibleMoves[1]
            else:
                if possibleMoves.count(True) == 2: #if the gene didn't find the way - forget his way
                    for tx, ty in moves:
                        Map[ty][tx] = 0
                        moves.remove((tx,ty))
                    break
                break

            tempY += diffY
            tempX += diffX

        stepCounter += 1

        if returnMap and drawAnimation:
            ax = plt.gca()

            summ = 3 * 0.75
            while summ >= 3 * 0.75:
                c1, c2, c3 = random.random(), random.random(), random.random()
                summ = c1 + c2 + c3

            randomColor = (c1, c2, c3)
            for movX, movY in moves:
                text = ax.text(movX + 0.5, size_y - 1 - movY + 0.5, Map[movY][movX], ha="center", va="center", color=randomColor, fontweight="bold")
                plt.pause(0.1)

    fitness = countFitness(Map, size_x, size_y)

    if returnMap == True:
        s = ""
        for row in Map:
            for col in row:
                s += ' K ' if col == -1 else colors[col % len(colors)] + '%2d ' % col + creset
            s += '\n'
        return s

    return fitness

def evaluatePopulation(size_x, size_y, Map, population):
    fitnesses = []
    for chrom in population: #summing all fitnesses
        fitnesses.append(evaluateChromosome(size_x, size_y, Map, chrom))
    return fitnesses

def createInitialPop(numOfGenes, size_x, size_y):
    chromosomes = []
    for chrom in range(population_size):
        genes = []
        for num in range(2 * (size_x + size_y)): #generating all genes on the perimeter
            if num < size_x:
                genes.append((num, 0, random.choice(['l', 'r'])))  # top
            elif num < size_x + size_y:
                genes.append((size_x - 1, num - size_x, random.choice(['l', 'r'])))  # right
            elif num < 2 * size_x + size_y:
                genes.append((num - size_x - size_y, size_y - 1, random.choice(['l', 'r'])))  # bottom
            else:
                genes.append((0, num - 2 * size_x - size_y, random.choice(['l', 'r'])))  # left

        random.shuffle(genes)

        chromosomes.append(genes[:numOfGenes])

    return chromosomes

def select_parent(chromosomes, fitnesses):
    if parent_selection == 1:  #Tournament Selection
        chrom_array = []
        for _ in range(0, 3): #choosing 3 chromosomes
            index = random.randint(0, population_size - 1)
            chrom_array.append(index)

        maxIndex = 0
        maxValue = fitnesses[chrom_array[0]]
        for chromIndex in range(1, len(chrom_array)):
            if fitnesses[chrom_array[chromIndex]] > maxValue: #looking for a max
                maxValue = fitnesses[chrom_array[chromIndex]]
                maxIndex = chrom_array[chromIndex]
        return chromosomes[maxIndex]
    else:  #Roulette Wheel Selection
        fitness_sum = 0
        for index in range(0, len(fitnesses)):  # checking each chromosome in a population
            fitness_sum += fitnesses[index] # counting total sum of the fitnesses

        index = 0
        num = 0
        number = random.randint(0, fitness_sum)  # choosing random number
        while num < number:
            num += fitnesses[index]
            index += 1

        index -= 1  # the previous chromosome was chosen

        return chromosomes[index]

def crossover(parent1, parent2, numOfGenes): #uniform crossover
    genes_1 = []
    genes_2 = []
    parents = [parent1, parent2]

    parentChoice = random.randint(0, 1) #choosing 'the starting' parent
    for geneIndex in range(numOfGenes): #for each gene we decide if we swap it or no
        if random.random() < 0.5:
            genes_1.append(parents[parentChoice][geneIndex])
            genes_2.append(parents[1 - parentChoice][geneIndex])
        else:
            genes_1.append(parents[1 - parentChoice][geneIndex])
            genes_2.append(parents[parentChoice][geneIndex])
    return genes_1, genes_2

def mutation(chromosome, numOfGenes, mut_rate, size_x, size_y):
    for _ in range(numOfGenes // 4): #try with 25% of genes
        if (random.random() < mut_rate): #to mutate genes with the probability of mut_rate
            geneIndex = random.randint(0, numOfGenes - 1) #choosing the gene to be mutated
            gene = chromosome[geneIndex]
            chance = random.random()
            if chance < 0.3: #maybe direction will be changed
                chromosome[geneIndex] = (gene[0], gene[1], random.choice(['l', 'r']))
            elif chance < 0.7: #choosing another gene and swaping them
                index2 = random.randint(0, numOfGenes - 1)
                while index2 == geneIndex:
                    index2 = random.randint(0, numOfGenes - 1)
                tmp = chromosome[index2]
                chromosome[index2] = chromosome[geneIndex]
                chromosome[geneIndex] = tmp
            else: #changing the coordinates
                side = random.randint(1, 4)
                if side == 1:  # left side
                    chromosome[geneIndex] = (0, random.randint(0, size_y - 1), gene[2])
                elif side == 2:  # top side
                    chromosome[geneIndex] = (random.randint(0, size_x - 1), 0, gene[2])
                elif side == 3:  # right side
                    chromosome[geneIndex] = (size_x - 1, random.randint(0, size_y - 1), gene[2])
                else:  # bottom side
                    chromosome[geneIndex] = (random.randint(0, size_x - 1), size_y - 1, gene[2])

def geneticAlgorithm(size_x, size_y, stones, tester=False):
    mutation_rate = mutation_min
    numOfStones = len(stones)
    numOfGenes = size_x + size_y + len(stones)
    max_possible_fitness = size_x * size_y - numOfStones
    if tester == True:
        data={"min":[],"max":[],"avg":[],"result":False}

    start = time.time()
    population = createInitialPop(numOfGenes, size_x, size_y)

    Map = createMap(size_x, size_y, stones)
    fitnesses = evaluatePopulation(size_x, size_y, Map, population)
    lastAverage = 0
    for generation in range(1, max_generations + 1):
        newChromosomes = []
        for __ in range(0, population_size // 2): #creating 2 new children population_size/2 times
            if random.random() < crossover_rate:
                parent1 = select_parent(population, fitnesses)
                parent2 = select_parent(population, fitnesses)

                children = crossover(parent1, parent2, numOfGenes)
                for i in range(2):
                    mutation(children[i], numOfGenes, mutation_rate, size_x, size_y)
                    newChromosomes.append(children[i])

        while len(newChromosomes) < population_size: #if population_size is an odd number
            newChromosomes.append(select_parent(population, fitnesses)) #add random chromosome from the previous population

        population = newChromosomes
        fitnesses = evaluatePopulation(size_x, size_y, Map, population)

        minValue = fitnesses[0]
        bestChromosome = population[0]
        maxValue = fitnesses[0]
        total = 0
        for index, fitness in enumerate(fitnesses):
            total += fitness
            if maxValue < fitness:
                maxValue = fitness
                bestChromosome = population[index]
            if minValue > fitness:
                minValue = fitness
        avgValue = total / population_size

        if (lastAverage and abs((lastAverage - avgValue) / lastAverage) < 0.02 and mutation_rate < mutation_max): #if fitness is not changing for too long
            mutation_rate += 0.01
        else:
            mutation_rate = mutation_min

        lastAverage = total / population_size

        if tester==True:
            data["min"].append(minValue)
            data["max"].append(maxValue)
            data["avg"].append(avgValue)

        print('generacia: %4d, max: %4d, best: %4d, min: %4d, avg: %7.2f, mutation_rate: %4.2f' % (generation, max_possible_fitness, maxValue, minValue, total / population_size, mutation_rate))

        if max_possible_fitness == maxValue:
            end = time.time()

            print(evaluateChromosome(size_x, size_y, Map, bestChromosome, True))
            print(" > Fitness - %d, Maximum possible fitness - %d" % (maxValue, max_possible_fitness))
            print(" > Total time: %.4f (s)         Number of generations: %d" % (end - start, generation))
            print()
            if tester == True:
                data["result"] = True
                return data
            return

    end = time.time()
    print(" > DID NOT FIND ANY SOLUTIONS IN THIS LIMIT")
    print(" > Best fitness - %d, Maximum possible fitness - %d" % (maxValue, max_possible_fitness))
    print(" > Total time: %.4f (s)         Number of generations: %d\n" % (end - start, generation))
    if tester == True:
        return data

def test():
    random.seed()
    size_x = 12 #can be changed
    size_y = 10 #can be changed
    stones = [(1, 2), (5, 1), (4, 3), (2, 4), (8, 6), (9, 6)] #can be changed
    global crossover_rate, population_size, mutation_min, mutation_max, parent_selection, drawAnimation
    drawAnimation = False

    print(colors[8], "Zvolte si typ testu:")
    print(colors[5], "1", colors[8], " - velky test na porovnanie parametrov")
    print(colors[5], "2", colors[8], " - test na porovnanie (ne)const mutacie")
    print(colors[5], "3", colors[8], " - test na porovnanie vyberu rodicov", creset)

    T = input()

    if T == '1':
        testSize = 20
        for parent_selection in [1,2]:
            for crossover_rate in [0.97,0.98,0.99]:
                for population_size in [25, 50, 75, 100, 150]:
                    for mutation_min in [0.01, 0.02]:
                        found = 0
                        genAvg = 0
                        for _ in range(testSize):
                            data = geneticAlgorithm(size_x, size_y, stones, True)
                            found += data["result"]
                            genAvg += len(data["avg"])
                        genAvg /= testSize
                        f = open("results_test.txt","a") #all data will be written in a results_test.txt
                        f.write("%d,%.2f,%d,%.2f,%d,%d,%.2f\n"%(parent_selection,crossover_rate,population_size, mutation_min, found, testSize, genAvg))
                        f.close()
                        print(colors[4],parent_selection,crossover_rate,population_size, mutation_min, found, testSize, genAvg, creset)
        return
    elif T == '2':
        seedNo = 777 #can be changed
    elif T == '3':
        seedNo = 123456789 #can be changed
        parent_selection = 1
    else:
        print(colors[5], "Zle zadane cislo!", creset)
        return

    random.seed(seedNo)
    data = geneticAlgorithm(size_x, size_y, stones, True)
    fig, (ax1,ax2) = plt.subplots(1,2)
    ax1.plot([i for i in range(1,len(data["max"])+1)], data["max"], '-', label="Maximalny fitness", linewidth=0.8)
    ax1.plot([i for i in range(1, len(data["avg"]) + 1)], data["avg"], '--', label="Priemerny fitness", linewidth=0.8)
    ax1.plot([i for i in range(1, len(data["min"]) + 1)], data["min"], '-', label="Minimalny fitness", linewidth=0.8)
    ax1.set_ylabel("Fitness")
    ax1.set_xlabel("Generacia")
    ax1.legend(loc='lower right')

    random.seed(seedNo)
    if T == '2':
        mutation_max = 0
    elif T == '3':
        parent_selection = 2

    data = geneticAlgorithm(size_x, size_y, stones, True)
    ax2.plot([i for i in range(1,len(data["max"])+1)], data["max"], '-', label="Maximalny fitness", linewidth=0.8)
    ax2.plot([i for i in range(1, len(data["avg"]) + 1)], data["avg"], '--', label="Priemerny fitness", linewidth=0.8)
    ax2.plot([i for i in range(1, len(data["min"]) + 1)], data["min"], '-', label="Minimalny fitness", linewidth=0.8)
    ax2.set_ylabel("Fitness")
    ax2.set_xlabel("Generacia")
    ax2.legend(loc='lower right')

    if T == '2':
        ax1.set_title("Meniaca sa mutacia")
        ax2.set_title("Konstantna mutacia")
    elif T == '3':
        ax1.set_title("Tournament (k=3) selekcia")
        ax2.set_title("Roulette selekcia")

    plt.show()

def change_parameters():
    global max_generations, crossover_rate, population_size, mutation_min, mutation_max, parent_selection

    print(colors[8], "Zadajte max pocet generacii", creset)
    max_generations = int(input())
    print(colors[8], "Zadajte max velkost populacii", creset)
    population_size = int(input())
    print(colors[8], "Zadajte pravdepodobnost krizenia", creset)
    crossover_rate = int(input())
    print(colors[8], "Zadajte min pravdepodobnost mutacii", creset)
    mutation_min = int(input())
    print(colors[8], "Zadajte max pravdepodobnost mutacii", creset)
    mutation_max = int(input())
    print(colors[8], "Vyberte typ selekcii rodicov: ", colors[5], "1", colors[8], "- Tournament", colors[5], "2", colors[8], "- Roulette", creset)
    parent_selection = int(input())

def main():
    random.seed()
    size_x = 12
    size_y = 10
    stones = []

    print(colors[8], "Stlacte ")
    print(colors[5], "1", colors[8], "- ak chcete vyuzit standartnu mapu")
    print(colors[5], "2", colors[8], "- ak chcete vytvorit svoju mapu", creset)
    M = input()

    if M == '1':
        print(colors[8], "Stlacte ", colors[5], "1", colors[8], ", ak chcete vymenit globalne parametre, alebo ine cislo, ak ich chcete nechat defaultne", creset)
        param = int(input())
        if param == 1:
            change_parameters()

        stones = [(1, 2), (5, 1), (4, 3), (2, 4), (8, 6), (9, 6)]

    elif M == '2':
        print(colors[8], "Stlacte ", colors[5], "1", colors[8], ", ak chcete vymenit globalne parametre, alebo ine cislo, ak ich chcete nechat defaultne", creset)
        param = int(input())
        if param == 1:
            change_parameters()

        print(colors[8], "Zadajte sirku zahrady (OX)", creset)
        size_x = int(input())
        print(colors[8], "Zadajte dlzku zahrady (OY)", creset)
        size_y = int(input())
        print(colors[8], "Zadajte pocet kamenov", creset)
        number_of_stones = int(input())
        print(colors[8], "Zadajte ich X Y suradnice", creset)
        for _ in range(0, number_of_stones):
            x, y = map(int, input().split())
            stones.append((x, y))
    else:
        print(colors[5], "Zle zadane cislo!", creset)
        return

    if drawAnimation:
        fig, ax = plt.subplots()
        ax.set_xticks([i for i in range(0, size_x + 1)])
        ax.set_xticks([i + 0.5 for i in range(0, size_x)], minor=True)
        ax.set_xticklabels([str(i) for i in range(0, size_x)], minor=True)

        ax.set_yticks([i for i in range(0, size_y + 1)])
        ax.set_yticks([i + 0.5 for i in range(0, size_y)], minor=True)
        ax.set_yticklabels([str(size_y - 1 - i) for i in range(0, size_y)], minor=True)
        ax.tick_params(which='both', length=0)
        plt.setp([ax.get_xmajorticklabels(), ax.get_ymajorticklabels()], visible=False)

        plt.grid(which='major')

    geneticAlgorithm(size_x, size_y, stones)

########################################################################################################################
print(colors[8], "Stlacte ", colors[5], "1", colors[8], ", ak chcete otestovat geneticky algoritmus, alebo ", colors[5], "2", colors[8], ", ak chcete spustit mnicha do zahradky.", creset)
start = input()
if start == '1':
    test()
elif start == '2':
    main()
else:
    print(colors[5], "Zle zadane cislo!", creset)