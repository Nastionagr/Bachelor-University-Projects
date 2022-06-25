#define _CRT_SECURE_NO_WARNINGS
#include <stdio.h>
#include <stdbool.h>
#include <stdlib.h>
#include <time.h>

#define WHTB "\033[47m"
#define WHT "\033[1;37m"
#define BLK "\033[1;30m"
#define UMAG "\033[4;35m"
#define YEL "\033[1;33m"
#define MAG "\033[1;35m"
#define RED "\033[1;31m"
#define CYN "\033[1;36m"
#define RESET "\033[0m"

#define random(x) (rand()%(x))
#define MAX_STEPS 20000000
#define MAX_TIME 15

clock_t TIME; //time of the DFS function
int STEPS; //total steps in function

typedef struct node
{
	int count; //number of the chess field on the way
	bool visited;

} NODE;

NODE*** initialization(int size)
{
	int x, y;
	NODE*** table; //2D array of NODEs

	table = (NODE***)malloc(size * sizeof(NODE**));

	for (y = 0; y < size; y++)
	{
		table[y] = (NODE**)malloc(size * sizeof(NODE*));
	}

	for (x = 0; x < size; x++)
	{
		for (y = 0; y < size; y++)
		{
			table[x][y] = (NODE*)malloc(sizeof(NODE));
			(table[x][y])->count = 0;
			(table[x][y])->visited = false;
		}
	}

	return table;
}

void reset_table(NODE*** table, int size, char* status)
{
	int x, y;

	for (x = 0; x < size; x++)
	{
		for (y = 0; y < size; y++)
		{
			(table[x][y])->count = 0;
			(table[x][y])->visited = false;
		}
	}

	*status = 0;
	STEPS = 0;

	return table;
}

void print_output(NODE*** table, int size, float duration)
{
	int x, y;

	printf("\n");
	printf(UMAG "Movement of the Euler's horse:" RESET);
	printf("\n");
	for (x = 0; x < size; x++)
	{
		for (y = 0; y < size; y++)
		{
			if ((x + y) % 2 == 0)
			{
				if ((table[x][y])->count < 10) printf(" "); //for text aligning
				printf(WHT" %d " RESET, (table[x][y])->count);
			}
			else
			{
				if ((table[x][y])->count < 10) printf(WHTB " "); //for text aligning
				printf(WHTB BLK" %d " RESET, (table[x][y])->count);
			}
		}
		printf("\n");
	}
	printf("\n");
	printf(MAG "Total steps: " WHT "%d" MAG "		Total time: " WHT "%.4lf\n" RESET, STEPS, duration);
}

void print_error(char status, int x, int y)
{
	if (status == 0) printf(RED "\nThe programm can't find any solutions for starting point (%d;%d).\n" RESET, x, y);
	else if (status == 2) printf(RED "\nTime limit exceeded for starting point (%d;%d).\n" RESET, x, y);
	else printf(RED "\nStep limit exceeded for starting point (%d;%d).\n" RESET, x, y);
}

void destroy_table(NODE*** table, int size)
{
	int x, y;

	for (y = 0; y < size; y++)
	{
		for (x = 0; x < size; x++)
		{
			free(table[x][y]);
		}
	}

	for (y = 0; y < size; y++)
	{
		free(table[y]);
	}

	free(table);
}

void DFS(NODE*** table, int size, int x, int y, int count, char* status)
{
	if ((clock() - TIME) / CLOCKS_PER_SEC >= MAX_TIME) //if it is longer than MAX_TIME (limit)
	{
		*status = 2;
		return;
	}

	if (STEPS >= MAX_STEPS) //if it performs more steps than MAX_STEPS (limit)
	{
		*status = 3;
		return;
	}

	(table[x][y])->count = count;
	(table[x][y])->visited = true;
	STEPS++;

	if (count == size * size) //if it is the last chess field
	{
		*status = 1;
		return;
	}

	//checking if unvisited neighbours exist; if the answer is YES - visit if (call DFS) 
	if (*status == 0 && x + 1 < size && y + 2 < size && (table[x + 1][y + 2])->visited == false) DFS(table, size, x + 1, y + 2, count + 1, status);
	if (*status == 0 && x + 2 < size && y + 1 < size && (table[x + 2][y + 1])->visited == false) DFS(table, size, x + 2, y + 1, count + 1, status);
	if (*status == 0 && x + 2 < size && y - 1 >= 0 && (table[x + 2][y - 1])->visited == false) DFS(table, size, x + 2, y - 1, count + 1, status);
	if (*status == 0 && x + 1 < size && y - 2 >= 0 && (table[x + 1][y - 2])->visited == false) DFS(table, size, x + 1, y - 2, count + 1, status);
	if (*status == 0 && x - 1 >= 0 && y - 2 >= 0 && (table[x - 1][y - 2])->visited == false) DFS(table, size, x - 1, y - 2, count + 1, status);
	if (*status == 0 && x - 2 >= 0 && y - 1 >= 0 && (table[x - 2][y - 1])->visited == false) DFS(table, size, x - 2, y - 1, count + 1, status);
	if (*status == 0 && x - 2 >= 0 && y + 1 < size && (table[x - 2][y + 1])->visited == false) DFS(table, size, x - 2, y + 1, count + 1, status);
	if (*status == 0 && x - 1 >= 0 && y + 2 < size && (table[x - 1][y + 2])->visited == false) DFS(table, size, x - 1, y + 2, count + 1, status);

	if (*status == 0) //if we didn't find an unvisited neighbour
	{
		(table[x][y])->visited = false;
		(table[x][y])->count = 0;
	}

	return;
}

int main()
{
	srand(time(NULL));
	int test, x, y, repeat;
	char status;
	NODE*** table;
	clock_t end;
	float duration;

	while (1)
	{
		printf("\n");
		printf(CYN "**********************************************************\n");
		printf(CYN "Choose the number of the test that you want to perform:\n");
		printf("(" YEL "5" CYN ") - size 5x5	(" YEL "6" CYN ") - size 6x6	(" YEL "7" CYN ") - size 7x7	(" YEL "0" CYN ") - exit\n" RESET);
		scanf("%d", &test);

		switch (test)
		{
		case 5:
			table = initialization(5);

			status = 0;
			STEPS = 0;
			TIME = clock();
			DFS(table, 5, 4, 0, 1, &status); //left down field
			end = clock();
			duration = (end - TIME) / (float)CLOCKS_PER_SEC;
			if (status == 1) print_output(table, 5, duration);
			else print_error(status, 4, 0);

			for (repeat = 1; repeat <= 4; repeat++)
			{
				reset_table(table, 5, &status);
				x = random(5);
				y = random(5);
				TIME = clock();
				DFS(table, 5, x, y, 1, &status);
				end = clock();
				duration = (end - TIME) / (float)CLOCKS_PER_SEC;
				if (status == 1) print_output(table, 5, duration);
				else print_error(status, x, y);
			}

			destroy_table(table, 5);
			break;

		case 6:
			table = initialization(6);

			status = 0;
			STEPS = 0;
			TIME = clock();
			DFS(table, 6, 5, 0, 1, &status); //left down field
			end = clock();
			duration = (end - TIME) / (float)CLOCKS_PER_SEC;
			if (status == 1) print_output(table, 6, duration);
			else print_error(status, 5, 0);

			for (repeat = 1; repeat <= 4; repeat++)
			{
				reset_table(table, 6, &status);
				x = random(6);
				y = random(6);
				TIME = clock();
				DFS(table, 6, x, y, 1, &status);
				end = clock();
				duration = (end - TIME) / (float)CLOCKS_PER_SEC;
				if (status == 1) print_output(table, 6, duration);
				else print_error(status, x, y);
			}

			destroy_table(table, 6);
			break;

		case 7:
			table = initialization(7);

			status = 0;
			STEPS = 0;
			TIME = clock();
			DFS(table, 7, 6, 0, 1, &status); //left down field
			end = clock();
			duration = (end - TIME) / (float)CLOCKS_PER_SEC;
			if (status == 1) print_output(table, 7, duration);
			else print_error(status, 6, 0);

			for (repeat = 1; repeat <= 4; repeat++)
			{
				reset_table(table, 7, &status);
				x = random(7);
				y = random(7);
				TIME = clock();
				DFS(table, 7, x, y, 1, &status);
				end = clock();
				duration = (end - TIME) / (float)CLOCKS_PER_SEC;
				if (status == 1) print_output(table, 7, duration);
				else print_error(status, x, y);
			}

			destroy_table(table, 7);
			break;

		case 0: return 0;

		default:
			printf(CYN "You put the wrong number.\n" RESET);
		}
	}

	return 0;
}